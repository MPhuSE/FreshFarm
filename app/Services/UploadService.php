<?php

namespace App\Services;

use App\Exceptions\ApiException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service dùng chung cho MỌI tính năng upload file trong hệ thống (không chỉ
 * ảnh sản phẩm) — validate MIME thật (dựa vào nội dung file qua fileinfo,
 * không dựa vào đuôi file client gửi lên vì có thể giả mạo), giới hạn dung
 * lượng, đặt tên ngẫu nhiên, và cung cấp hàm xóa file khi cần rollback.
 */
class UploadService
{
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5MB

    /**
     * Validate + lưu 1 file ảnh vào disk "public", trả về đường dẫn tương đối
     * (để lưu vào cột file_path trong DB, KHÔNG lưu full URL).
     *
     * @throws ApiException UNSUPPORTED_MEDIA (415) | FILE_TOO_LARGE (413)
     */
    public function storeImage(UploadedFile $file, string $directory): string
    {
        // getMimeType() đọc MIME thật từ nội dung file (qua ext-fileinfo),
        // không phải suy ra từ đuôi .jpg/.png client gửi lên — đáp ứng đúng
        // yêu cầu "kiểm tra MIME thật" trong quy tắc BR-11.
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw new ApiException(
                'Định dạng ảnh không được hỗ trợ. Chỉ chấp nhận JPG, PNG, WebP.',
                'UNSUPPORTED_MEDIA',
                415
            );
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new ApiException(
                'Dung lượng ảnh vượt quá giới hạn cho phép (tối đa 5MB).',
                'FILE_TOO_LARGE',
                413
            );
        }

        // Đặt tên ngẫu nhiên bằng UUID + giữ lại đúng phần mở rộng gốc,
        // tránh trùng tên và tránh lộ tên file gốc do người dùng đặt (BR-11).
        $extension = $file->getClientOriginalExtension();
        $randomName = Str::uuid()->toString().'.'.$extension;

        // Lưu vào disk "public" (đã cấu hình sẵn trong config/filesystems.php),
        // storage:link đã trỏ public/storage -> storage/app/public.
        $path = $file->storeAs($directory, $randomName, 'public');

        return $path;
    }

    /**
     * Xóa 1 hoặc nhiều file vật lý — dùng khi DB Transaction rollback để dọn
     * "orphan file" (file đã lưu ổ đĩa nhưng DB insert thất bại giữa chừng).
     */
    public function delete(string|array $paths): void
    {
        Storage::disk('public')->delete($paths);
    }
}