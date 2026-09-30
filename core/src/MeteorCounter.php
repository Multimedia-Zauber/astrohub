<?php

declare(strict_types=1);
namespace AstroHub\Core;
use PDO;

final class MeteorCounter {
 public function __construct(private PDO $db) {}
 public function start(array $in): array { $id='met_'.bin2hex(random_bytes(8)); $now=gmdate(DATE_ATOM); $s=$this->db->prepare('INSERT INTO meteor_sessions(id,profile_id,location_id,name,started_at,notes,created_at) VALUES(?,?,?,?,?,?,?)'); $s->execute([$id,$in['profile_id']??'local',$in['location_id']??null,$in['name']??'Meteor Session',$in['started_at']??$now,$in['notes']??null,$now]); return $this->session($id); }
 public function record(string $sessionId,array $in): array { $id=(string)($in['id']??('evt_'.bin2hex(random_bytes(8)))); $ms=(int)($in['observed_at_ms']??round(microtime(true)*1000)); $iso=(string)($in['observed_at']??gmdate('Y-m-d\TH:i:s.',intdiv($ms,1000)).sprintf('%03dZ',$ms%1000)); $s=$this->db->prepare('INSERT OR IGNORE INTO meteor_events(id,session_id,observed_at,observed_at_ms,input_method,magnitude,shower,notes,created_at) VALUES(?,?,?,?,?,?,?,?,?)'); $s->execute([$id,$sessionId,$iso,$ms,$in['input_method']??'touch',$in['magnitude']??null,$in['shower']??null,$in['notes']??null,gmdate(DATE_ATOM)]); return ['event_id'=>$id,'session_id'=>$sessionId,'observed_at'=>$iso,'observed_at_ms'=>$ms,'count'=>$this->count($sessionId)]; }
 public function end(string $id): array { $this->db->prepare('UPDATE meteor_sessions SET ended_at=? WHERE id=?')->execute([gmdate(DATE_ATOM),$id]); return $this->session($id); }
 public function session(string $id): array { $s=$this->db->prepare('SELECT * FROM meteor_sessions WHERE id=?');$s->execute([$id]);$session=$s->fetch(); if(!$session) throw new \RuntimeException('Meteor session not found'); $e=$this->db->prepare('SELECT id,observed_at,observed_at_ms,input_method,magnitude,shower,notes FROM meteor_events WHERE session_id=? ORDER BY observed_at_ms');$e->execute([$id]);$session['events']=$e->fetchAll();$session['count']=count($session['events']);return $session; }
 public function export(string $id,string $format): string { $d=$this->session($id); if($format==='json') return json_encode($d,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES); $f=fopen('php://temp','r+'); fputcsv($f,['event_id','observed_at','observed_at_ms','input_method','magnitude','shower','notes']); foreach($d['events'] as $e) fputcsv($f,array_values($e)); rewind($f); return stream_get_contents($f) ?: ''; }
 private function count(string $id): int { $s=$this->db->prepare('SELECT COUNT(*) FROM meteor_events WHERE session_id=?');$s->execute([$id]);return (int)$s->fetchColumn(); }
}
