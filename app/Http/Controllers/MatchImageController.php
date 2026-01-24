<?php

namespace App\Http\Controllers;

use App\Models\MatchImage;
use App\Models\MVP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MatchImageController extends Controller
{
    private $maxWidth = 1200;
    private $maxHeight = 1200;
    private $quality = 75;


    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string|starts_with:data:image/',
                'match_id' => 'required|exists:matches,id',
            ]);
            $imageData = $request->image;
            preg_match('/data:image\/(\w+);base64,/', $imageData, $matches);
            $extension = $matches[1] ?? null;
            if (!$extension) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format.',
                ], 422);
            }

            $result = $this->processImage($imageData, $extension);
            
            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }
            $matchImage = MatchImage::create([
                'match_id' => $request->match_id,
                'image_path' => $result['filename'],
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded and compressed successfully',
                'imageUrl' => '/uploads/match_images/' . $result['filename'],
                'imageSize' => $result['fileSize'],
                'dimensions' => $result['dimensions']
            ]);

        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getMatchImages($matchId)
    {
        try {
            $images = MatchImage::where('match_id', $matchId)
                ->orderBy('created_at', 'desc')
                ->get(['image_path']);

            return response()->json([
                'success' => true,
                'images' => $images->pluck('image_path')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch images: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $image = MatchImage::findOrFail($id);
            Storage::disk('public')->delete('match_images/' . $image->image_path);
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processImage($imageData, $extension)
    {
        try {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $decodedImage = base64_decode($imageData);

            

            if (!$decodedImage) {
                return [
                    'success' => false,
                    'message' => 'Invalid Base64 image data.'
                ];
            }


            $tempFile = tempnam(sys_get_temp_dir(), 'img');
            file_put_contents($tempFile, $decodedImage);

            if (strtolower($extension) === 'heic') {
                try {
                    $convertedFile = HeicConverter::convert($tempFile);
                    if (!$convertedFile) {
                        \Log::error('HEIC Conversion Failed', [
                            'original_file' => $tempFile,
                            'extension' => $extension,
                            'file_size' => filesize($tempFile),
                            'file_contents' => bin2hex(file_get_contents($tempFile))
                        ]);
    
                        return [
                            'success' => false,
                            'message' => 'Failed to convert HEIC image. Unsupported format or corrupt file.'
                        ];
                    }
                    $tempFile = $convertedFile;
                    $extension = 'jpg';
                } catch (\Exception $conversionException) {
                    \Log::error('HEIC Conversion Exception', [
                        'message' => $conversionException->getMessage(),
                        'trace' => $conversionException->getTraceAsString(),
                        'original_file' => $tempFile
                    ]);
    
                    return [
                        'success' => false,
                        'message' => 'Unexpected error during HEIC conversion: ' . $conversionException->getMessage()
                    ];
                }
            }

            $image = Image::make($tempFile);

            $decodedImage = null;
            gc_collect_cycles();

            if ($image->width() > $this->maxWidth || $image->height() > $this->maxHeight) {
                $image->resize($this->maxWidth, $this->maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $filename = time() . '_' . uniqid() . '.' . $extension;
            $storagePath = public_path('uploads/match_images');

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            $fullPath = $storagePath . '/' . $filename;
            
            $image->save($fullPath, $this->quality);

            $dimensions = [
                'width' => $image->width(),
                'height' => $image->height()
            ];

            $image->destroy();
            unlink($tempFile);
            gc_collect_cycles();

            return [
                'success' => true,
                'filename' => $filename,
                'fileSize' => $this->formatFileSize(filesize($fullPath)),
                'dimensions' => $dimensions
            ];

        } catch (\Exception $e) {
            \Log::error('Image processing error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'extension' => $extension
            ]);

        return [
            'success' => false,
            'message' => 'Image processing failed: ' . $e->getMessage()
        ];
        }
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
}