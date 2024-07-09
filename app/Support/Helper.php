<?php

namespace App\Support;

use Google_Client;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use stdClass;

class Helper
{
    public static function arrayToObject(array $array): stdClass
    {
        $object = new stdClass();

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::arrayToObject($value);
            }

            $object->$key = $value;
        }

        return $object;
    }

    public static function getPresignedUrl(UploadedFile|array $file): string
    {
        AwsS3V3Adapter::macro('getClient', fn () => $this->client);

        $s3Client = Storage::disk('s3')->getClient();

        $filename = (string) Str::uuid();

        if ($file instanceof UploadedFile) {
            $directory = Str::startsWith($file->getMimeType(), 'image/') ? 'images' : 'files';
            $filetype = $file->getMimeType();
        } else {
            $directory = Str::startsWith($file['filetype'], 'image/') ? 'images' : 'files';
            $filetype = $file['filetype'];
        }

        $command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $directory.'/'.$filename,
            // 'ACL' => 'public-read',
            'ContentType' => $filetype,
        ]);
        $presignedUrl = (string) $s3Client->createPresignedRequest($command, '+20 minutes')->getUri();

        return $presignedUrl;
    }

    public static function uploadToS3(UploadedFile $file): string
    {
        /** @var Illuminate\Support\Facades\Storage */
        $storage = Storage::disk('s3');

        if (str_starts_with($file->getMimeType(), 'image/')) {
            // Get the temporary path of the file
            $tempPath = tempnam(sys_get_temp_dir(), 'upload');
            file_put_contents($tempPath, file_get_contents($file->getRealPath()));

            // Optimize the image using Spatie Image Optimizer
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($tempPath);

            // Store the optimized image to S3
            $path = $storage->putFile('images', new File($tempPath));
        } else {
            $path = $storage->putFile('files', $file);
        }

        $url = $storage->url($path);

        return $url;
    }

    public static function wrapPaginationInfosInMeta(Paginator|LengthAwarePaginator|CursorPaginator $paginator)
    {
        $paginator = $paginator->toArray();

        $data = $paginator['data'];
        unset($paginator['data']);

        return [
            'data' => $data,
            'meta' => $paginator,
        ];
    }

    public static function testIdToken($token)
    {
        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        return $client->verifyIdToken($token);
    }

    public static function log($data)
    {
        Log::channel('db')->info(json_encode($data));
    }
}
