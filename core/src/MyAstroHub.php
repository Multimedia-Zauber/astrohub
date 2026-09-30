<?php

declare(strict_types=1);

namespace AstroHub\Core;

use PDO;

final class MyAstroHub
{
    public function __construct(private PDO $db) {}

    public function bootstrapProfile(array $input): array
    {
        $profileId = (string)($input['id'] ?? 'local');
        $now = gmdate(DATE_ATOM);
        $stmt = $this->db->prepare('INSERT INTO profiles (id,name,language,experience_level,created_at,updated_at)
            VALUES (:id,:name,:language,:level,:created,:updated)
            ON CONFLICT(id) DO UPDATE SET name=excluded.name, language=excluded.language,
            experience_level=excluded.experience_level, updated_at=excluded.updated_at');
        $stmt->execute([':id'=>$profileId, ':name'=>(string)($input['name'] ?? 'Astronom'), ':language'=>(string)($input['language'] ?? 'de'), ':level'=>(string)($input['level'] ?? 'simple'), ':created'=>$now, ':updated'=>$now]);

        $this->db->prepare('DELETE FROM interests WHERE profile_id=?')->execute([$profileId]);
        $interestStmt = $this->db->prepare('INSERT OR IGNORE INTO interests (profile_id,interest) VALUES (?,?)');
        foreach (($input['interests'] ?? []) as $interest) $interestStmt->execute([$profileId, (string)$interest]);
        return $this->overview($profileId);
    }

    public function addWorkspace(string $profileId, array $input): array
    {
        $id = 'ws_' . bin2hex(random_bytes(6));
        $stmt = $this->db->prepare('INSERT INTO workspaces (id,profile_id,name,type,icon,is_default,created_at) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$id,$profileId,(string)$input['name'],(string)($input['type'] ?? 'observation'),(string)($input['icon'] ?? '🔭'),(int)($input['is_default'] ?? 0),gmdate(DATE_ATOM)]);
        return $this->overview($profileId);
    }

    public function addLocation(string $profileId, array $input): array
    {
        $id = 'loc_' . bin2hex(random_bytes(6));
        $stmt = $this->db->prepare('INSERT INTO locations (id,profile_id,name,latitude,longitude,elevation_m,timezone,is_default,privacy,created_at) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$id,$profileId,(string)$input['name'],(float)$input['latitude'],(float)$input['longitude'],isset($input['elevation_m'])?(float)$input['elevation_m']:null,$input['timezone'] ?? null,(int)($input['is_default'] ?? 0),(string)($input['privacy'] ?? 'private'),gmdate(DATE_ATOM)]);
        return $this->overview($profileId);
    }

    public function addSignal(string $profileId, array $input): void
    {
        $stmt = $this->db->prepare('INSERT INTO preference_signals (profile_id,signal_type,subject,weight,created_at) VALUES (?,?,?,?,?)');
        $stmt->execute([$profileId,(string)$input['signal_type'],(string)$input['subject'],(float)($input['weight'] ?? 1),gmdate(DATE_ATOM)]);
    }

    public function overview(string $profileId): array
    {
        $profile = $this->db->prepare('SELECT id,name,language,experience_level FROM profiles WHERE id=?');
        $profile->execute([$profileId]);
        $workspaces = $this->db->prepare('SELECT id,name,type,icon,is_default FROM workspaces WHERE profile_id=? ORDER BY is_default DESC, created_at');
        $workspaces->execute([$profileId]);
        $locations = $this->db->prepare('SELECT id,name,latitude,longitude,elevation_m,timezone,is_default,privacy FROM locations WHERE profile_id=? ORDER BY is_default DESC, created_at');
        $locations->execute([$profileId]);
        $interests = $this->db->prepare('SELECT interest FROM interests WHERE profile_id=? ORDER BY interest');
        $interests->execute([$profileId]);
        $signals = $this->db->prepare('SELECT subject, SUM(weight) score FROM preference_signals WHERE profile_id=? GROUP BY subject ORDER BY score DESC LIMIT 8');
        $signals->execute([$profileId]);
        return ['profile'=>$profile->fetch() ?: null,'interests'=>array_column($interests->fetchAll(),'interest'),'workspaces'=>$workspaces->fetchAll(),'locations'=>$locations->fetchAll(),'personal_engine'=>['signals'=>$signals->fetchAll(),'mode'=>'local-user-controlled']];
    }
}
