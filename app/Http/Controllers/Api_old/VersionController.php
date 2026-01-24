<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'current_version' => 'required|string',
            'platform' => 'required|in:ios,android'
        ]);

        $latestVersion = AppVersion::where('platform', $request->platform)
            ->latest('release_date')
            ->first();

        if (!$latestVersion) {
            return response()->json([
                'update_required' => false
            ]);
        }

        $needsUpdate = $this->compareVersions(
            $request->current_version, 
            $latestVersion->version
        );

        return response()->json([
            'update_required' => $needsUpdate,
            'latest_version' => $latestVersion->version,
            'force_update' => $latestVersion->is_force_update,
            'description' => $latestVersion->description,
            'store_url' => $this->getStoreUrl($request->platform)
        ]);
    }

    private function compareVersions($currentVersion, $latestVersion)
    {
        $current = explode('.', $currentVersion);
        $latest = explode('.', $latestVersion);

        for ($i = 0; $i < count($latest); $i++) {
            $currentPart = isset($current[$i]) ? (int)$current[$i] : 0;
            $latestPart = (int)$latest[$i];

            if ($latestPart > $currentPart) {
                return true;
            } elseif ($latestPart < $currentPart) {
                return false;
            }
        }

        return false;
    }

    private function getStoreUrl($platform)
    {
        return $platform === 'ios'
            ? 'https://apps.apple.com/us/app/pitch-burners/id6740053781'
            : 'https://play.google.com/store/apps/details?id=com.dsignzmedia.pitchBurnersCricketLeague';
    }
}