<?php

declare(strict_types=1);

namespace AstroHub\Core;

final class UpdateCenter
{
    public function __construct(
        private string $repository = 'Multimedia-Zauber/astrohub',
        private string $currentVersion = '0.1.0-alpha'
    ) {}

    public function status(): array
    {
        $release = $this->githubJson('https://api.github.com/repos/' . $this->repository . '/releases/latest');
        if (!$release || empty($release['tag_name'])) {
            return ['status'=>'offline','current_version'=>$this->currentVersion,'update_available'=>false,'message'=>'GitHub currently unavailable or no release published.'];
        }
        $latest = ltrim((string)$release['tag_name'], 'v');
        return [
            'status'=>'ok',
            'current_version'=>$this->currentVersion,
            'latest_version'=>$latest,
            'update_available'=>$this->isNewer($latest, $this->currentVersion),
            'release_name'=>$release['name'] ?? $release['tag_name'],
            'published_at'=>$release['published_at'] ?? null,
            'release_url'=>$release['html_url'] ?? null,
            'notes'=>$release['body'] ?? '',
            'prerelease'=>(bool)($release['prerelease'] ?? false),
        ];
    }

    public function dataManifest(): array
    {
        $url = 'https://raw.githubusercontent.com/' . $this->repository . '/main/data-packages/manifest.json';
        return $this->githubJson($url) ?: ['schema'=>1,'packages'=>[],'status'=>'offline'];
    }

    private function githubJson(string $url): ?array
    {
        $context = stream_context_create(['http'=>['timeout'=>4,'ignore_errors'=>true,'header'=>"User-Agent: AstroHub-UpdateCenter\r\nAccept: application/vnd.github+json\r\n"]]);
        $raw = @file_get_contents($url, false, $context);
        if ($raw === false) return null;
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    private function isNewer(string $latest, string $current): bool
    {
        $clean = static fn(string $v): string => preg_replace('/[^0-9.].*$/', '', ltrim($v, 'v')) ?: '0.0.0';
        return version_compare($clean($latest), $clean($current), '>');
    }
}
