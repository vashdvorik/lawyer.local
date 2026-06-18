<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Отображение профиля пользователя
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $courses = Course::query()
            ->availableTo($user)
            ->with('materials')
            ->orderBy('title')
            ->get();

        return view('pages.profile', [
            'user' => $user,
            'courses' => $courses,
        ]);
    }

    /**
     * Выдача аватара пользователя без зависимости от storage symlink
     */
    public function avatar(User $user)
    {
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            return Storage::disk('public')->response($user->avatar);
        }

        return response()->file(public_path('assets/images/profile/1.png'));
    }

    /**
     * Отображение формы редактирования профиля
     */
    public function edit()
    {
        return view('pages.profile-edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Обновление профиля пользователя
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'avatar' => ['nullable', 'image', 'max:20480'], // до 20 МБ
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->hasFile('avatar')) {
            // Удалить старый аватар при его наличии
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Сохранить новый аватар
            $path = $request->file('avatar')->store('avatars', 'public');

            // Сжать изображение
            $absolutePath = storage_path('app/public/'.$path);
            $this->resizeAndCompressImage($absolutePath);

            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Профиль обновлён.');
    }

    /**
     * Изменение размера, сжатие и обрезка изображения под пропорции 4:3 с учетом EXIF-ориентации
     */
    private function resizeAndCompressImage(string $filePath, int $maxWidth = 400, int $quality = 75): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            return;
        }

        [$width, $height, $type] = $imageInfo;

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $srcImage = @imagecreatefromgif($filePath);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = @imagecreatefromwebp($filePath);
                break;
            default:
                return;
        }

        if (! $srcImage) {
            return;
        }

        // 1. Исправление ориентации по метаданным EXIF (только для JPEG)
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            if ($exif && ! empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $srcImage = imagerotate($srcImage, 180, 0);
                        break;
                    case 6:
                        $srcImage = imagerotate($srcImage, 270, 0);
                        $temp = $width;
                        $width = $height;
                        $height = $temp;
                        break;
                    case 8:
                        $srcImage = imagerotate($srcImage, 90, 0);
                        $temp = $width;
                        $width = $height;
                        $height = $temp;
                        break;
                }
            }
        }

        // 2. Обрезка до пропорций 3:4
        $targetRatio = 3 / 4;
        $currentRatio = $width / $height;

        if ($currentRatio > $targetRatio) {
            // Изображение шире, чем 4:3 (обрезаем по бокам)
            $cropWidth = (int) ($height * $targetRatio);
            $cropHeight = $height;
            $x = (int) (($width - $cropWidth) / 2);
            $y = 0;
        } else {
            // Изображение выше, чем 4:3 (обрезаем сверху и снизу)
            $cropWidth = $width;
            $cropHeight = (int) ($width / $targetRatio);
            $x = 0;
            $y = (int) (($height - $cropHeight) / 2);
        }

        $croppedImage = imagecrop($srcImage, [
            'x' => $x,
            'y' => $y,
            'width' => $cropWidth,
            'height' => $cropHeight,
        ]);

        if ($croppedImage !== false) {
            imagedestroy($srcImage);
            $srcImage = $croppedImage;
            $width = $cropWidth;
            $height = $cropHeight;
        }

        // 3. Изменение размера до максимальной ширины
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) (($height / $width) * $maxWidth);
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Сохранение прозрачности для PNG, GIF и WEBP
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF || $type === IMAGETYPE_WEBP) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Сохранение обратно в файл
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dstImage, $filePath, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($dstImage, $filePath, 8);
                break;
            case IMAGETYPE_GIF:
                imagegif($dstImage, $filePath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($dstImage, $filePath, $quality);
                break;
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);
    }

    /**
     * Обновление пароля пользователя
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Пароль изменён.');
    }
}
