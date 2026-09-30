import React, { useEffect, useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import './styles.css';

type Theme = 'dark' | 'night';
type Language = 'de' | 'en' | 'fr';
type Level = 'simple' | 'advanced' | 'science';
type Interest = 'observe' | 'photo' | 'meteors' | 'aurora' | 'sun' | 'science' | 'hardware';

type Health = {
  status: string;
  version: string;
  database: string;
  science: string;
};

const interests: { id: Interest; icon: string; label: Record<Language, string> }[] = [
  { id: 'observe', icon: '🔭', label: { de: 'Beobachten', en: 'Observing', fr: 'Observer' } },
  { id: 'photo', icon: '📸', label: { de: 'Astrofotografie', en: 'Astrophotography', fr: 'Astrophotographie' } },
  { id: 'meteors', icon: '☄️', label: { de: 'Meteore', en: 'Meteors', fr: 'Météores' } },
  { id: 'aurora', icon: '🌌', label: { de: 'Polarlicht', en: 'Aurora', fr: 'Aurores' } },
  { id: 'sun', icon: '☀️', label: { de: 'Sonne', en: 'Sun', fr: 'Soleil' } },
  { id: 'science', icon: '🔬', label: { de: 'Wissenschaft', en: 'Science', fr: 'Science' } },
  { id: 'hardware', icon: '🛠️', label: { de: 'Hardware', en: 'Hardware', fr: 'Matériel' } },
];

const copy = {
  de: {
    tagline: 'Deine offene Astronomie-Plattform', start: "Los geht's", free: 'Kostenlos · Open Source · Local First',
    language: 'Sprache', interests: 'Was interessiert dich?', interestsHint: 'Du kannst mehrere Bereiche auswählen.',
    level: 'Wie möchtest du AstroHub verwenden?', simple: 'Einfach', simpleText: 'AstroHub erklärt mir die wichtigsten Informationen.',
    advanced: 'Erweitert', advancedText: 'Ich möchte zusätzliche astronomische Daten sehen.', science: 'Wissenschaftlich', scienceText: 'Ich benötige Rohdaten und wissenschaftliche Werkzeuge.',
    location: 'Wo beobachtest du normalerweise?', place: 'Ort oder Standortname', lat: 'Breitengrad', lon: 'Längengrad', later: 'Später einrichten',
    workspace: 'Dein erster Workspace', workspaceHint: 'AstroHub schlägt ihn anhand deiner Interessen vor.', finish: 'AstroHub starten', back: 'Zurück', next: 'Weiter',
    night: 'Astro Night', dark: 'Dark', status: 'Systemstatus', core: 'Core', database: 'SQLite', scienceService: 'Science', ready: 'Dein AstroHub ist bereit.', reset: 'Onboarding erneut öffnen',
  },
  en: {
    tagline: 'Your open astronomy platform', start: 'Get started', free: 'Free · Open Source · Local First', language: 'Language', interests: 'What interests you?', interestsHint: 'Choose more than one area.',
    level: 'How would you like to use AstroHub?', simple: 'Simple', simpleText: 'AstroHub explains the most important information.', advanced: 'Advanced', advancedText: 'I want to see additional astronomy data.', science: 'Scientific', scienceText: 'I need raw data and scientific tools.',
    location: 'Where do you usually observe?', place: 'Place or location name', lat: 'Latitude', lon: 'Longitude', later: 'Set up later', workspace: 'Your first workspace', workspaceHint: 'AstroHub suggests it from your interests.', finish: 'Start AstroHub', back: 'Back', next: 'Continue', night: 'Astro Night', dark: 'Dark', status: 'System status', core: 'Core', database: 'SQLite', scienceService: 'Science', ready: 'Your AstroHub is ready.', reset: 'Open onboarding again',
  },
  fr: {
    tagline: "Votre plateforme d'astronomie ouverte", start: 'Commencer', free: 'Gratuit · Open Source · Local First', language: 'Langue', interests: 'Qu’est-ce qui vous intéresse ?', interestsHint: 'Vous pouvez choisir plusieurs domaines.',
    level: 'Comment souhaitez-vous utiliser AstroHub ?', simple: 'Simple', simpleText: "AstroHub m'explique les informations essentielles.", advanced: 'Avancé', advancedText: 'Je souhaite voir davantage de données astronomiques.', science: 'Scientifique', scienceText: "J'ai besoin de données brutes et d'outils scientifiques.",
    location: "Où observez-vous habituellement ?", place: 'Lieu ou nom du site', lat: 'Latitude', lon: 'Longitude', later: 'Configurer plus tard', workspace: 'Votre premier espace', workspaceHint: 'AstroHub le propose selon vos intérêts.', finish: 'Démarrer AstroHub', back: 'Retour', next: 'Continuer', night: 'Astro Night', dark: 'Dark', status: 'État du système', core: 'Core', database: 'SQLite', scienceService: 'Science', ready: 'Votre AstroHub est prêt.', reset: "Relancer l'accueil",
  },
} as const;

function App() {
  const [theme, setTheme] = useState<Theme>(() => (localStorage.getItem('astrohub.theme') as Theme) || 'dark');
  const [language, setLanguage] = useState<Language>(() => (localStorage.getItem('astrohub.language') as Language) || 'de');
  const [step, setStep] = useState(() => Number(localStorage.getItem('astrohub.onboarding.complete')) ? 6 : 0);
  const [selected, setSelected] = useState<Interest[]>([]);
  const [level, setLevel] = useState<Level>('simple');
  const [place, setPlace] = useState('');
  const [latitude, setLatitude] = useState('');
  const [longitude, setLongitude] = useState('');
  const [health, setHealth] = useState<Health | null>(null);

  const t = copy[language];
  const workspace = useMemo(() => selected.includes('photo') ? '📸 My Foto' : selected.includes('meteors') ? '☄️ My Meteore' : selected.includes('science') ? '🔬 My Science' : '🔭 My Beobachtung', [selected]);

  useEffect(() => {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('astrohub.theme', theme);
  }, [theme]);

  useEffect(() => {
    document.documentElement.lang = language;
    localStorage.setItem('astrohub.language', language);
  }, [language]);

  useEffect(() => {
    fetch('http://127.0.0.1:8080/api/v1/health').then(r => r.ok ? r.json() : Promise.reject()).then(setHealth).catch(() => setHealth(null));
  }, []);

  const toggleInterest = (id: Interest) => setSelected(current => current.includes(id) ? current.filter(item => item !== id) : [...current, id]);
  const finish = () => {
    localStorage.setItem('astrohub.onboarding.complete', '1');
    localStorage.setItem('astrohub.profile', JSON.stringify({ language, interests: selected, level, location: { place, latitude, longitude }, workspace }));
    setStep(6);
  };

  return (
    <main className="app-shell">
      <header className="topbar">
        <strong>🌌 AstroHub</strong>
        <div className="theme-switch" aria-label="Display mode">
          <button className={theme === 'dark' ? 'active' : ''} onClick={() => setTheme('dark')}>🌙 {t.dark}</button>
          <button className={theme === 'night' ? 'active' : ''} onClick={() => setTheme('night')}>🔴 {t.night}</button>
        </div>
      </header>

      <section className="panel">
        {step === 0 && <div className="welcome">
          <div className="orb">✦</div><p className="kicker">0.1 Alpha</p><h1>AstroHub</h1><p className="lead">{t.tagline}</p>
          <button className="primary" onClick={() => setStep(1)}>{t.start} →</button><p className="quiet">{t.free}</p><p className="signature">Developed by Multimedia Zauber</p>
        </div>}

        {step === 1 && <Wizard title={t.language} step={1} back={() => setStep(0)} next={() => setStep(2)} labels={t}>
          <div className="choice-grid three"><Choice active={language === 'de'} onClick={() => setLanguage('de')} title="🇩🇪 Deutsch"/><Choice active={language === 'en'} onClick={() => setLanguage('en')} title="🇬🇧 English"/><Choice active={language === 'fr'} onClick={() => setLanguage('fr')} title="🇫🇷 Français"/></div>
        </Wizard>}

        {step === 2 && <Wizard title={t.interests} subtitle={t.interestsHint} step={2} back={() => setStep(1)} next={() => setStep(3)} labels={t}>
          <div className="choice-grid">{interests.map(item => <Choice key={item.id} active={selected.includes(item.id)} onClick={() => toggleInterest(item.id)} title={`${item.icon} ${item.label[language]}`}/>)}</div>
        </Wizard>}

        {step === 3 && <Wizard title={t.level} step={3} back={() => setStep(2)} next={() => setStep(4)} labels={t}>
          <div className="stack"><Choice active={level === 'simple'} onClick={() => setLevel('simple')} title={`🌱 ${t.simple}`} text={t.simpleText}/><Choice active={level === 'advanced'} onClick={() => setLevel('advanced')} title={`🔭 ${t.advanced}`} text={t.advancedText}/><Choice active={level === 'science'} onClick={() => setLevel('science')} title={`🔬 ${t.science}`} text={t.scienceText}/></div>
        </Wizard>}

        {step === 4 && <Wizard title={t.location} step={4} back={() => setStep(3)} next={() => setStep(5)} labels={t}>
          <div className="form"><label>{t.place}<input value={place} onChange={e => setPlace(e.target.value)} placeholder="Gurnigel"/></label><div className="coords"><label>{t.lat}<input inputMode="decimal" value={latitude} onChange={e => setLatitude(e.target.value)}/></label><label>{t.lon}<input inputMode="decimal" value={longitude} onChange={e => setLongitude(e.target.value)}/></label></div><button className="text-button" onClick={() => { setPlace(''); setLatitude(''); setLongitude(''); setStep(5); }}>{t.later}</button></div>
        </Wizard>}

        {step === 5 && <Wizard title={t.workspace} subtitle={t.workspaceHint} step={5} back={() => setStep(4)} next={finish} nextLabel={t.finish} labels={t}>
          <div className="workspace-card"><span>WORKSPACE</span><strong>{workspace}</strong><p>{place || 'Local First'} · {level}</p></div>
        </Wizard>}

        {step === 6 && <div className="dashboard"><p className="kicker">{workspace}</p><h1>{t.ready}</h1><div className="status-card"><h2>{t.status}</h2><div className="status-row"><span>{t.core}</span><b>{health?.status ?? 'offline'}</b></div><div className="status-row"><span>{t.database}</span><b>{health?.database ?? 'offline'}</b></div><div className="status-row"><span>{t.scienceService}</span><b>{health?.science ?? 'offline'}</b></div></div><button className="secondary" onClick={() => { localStorage.removeItem('astrohub.onboarding.complete'); setStep(0); }}>{t.reset}</button></div>}
      </section>
      <footer>{t.free} · Developed by Multimedia Zauber</footer>
    </main>
  );
}

function Choice({ active, onClick, title, text }: { active: boolean; onClick: () => void; title: string; text?: string }) {
  return <button className={`choice ${active ? 'selected' : ''}`} onClick={onClick}><strong>{title}</strong>{text && <span>{text}</span>}</button>;
}

function Wizard({ title, subtitle, step, back, next, nextLabel, labels, children }: { title: string; subtitle?: string; step: number; back: () => void; next: () => void; nextLabel?: string; labels: typeof copy.de; children: React.ReactNode }) {
  return <div className="wizard"><div className="progress"><span style={{ width: `${step * 20}%` }}/></div><p className="kicker">ASTROHUB SETUP · {step}/5</p><h1>{title}</h1>{subtitle && <p className="subtitle">{subtitle}</p>}<div className="wizard-body">{children}</div><div className="actions"><button className="secondary" onClick={back}>← {labels.back}</button><button className="primary" onClick={next}>{nextLabel || labels.next} →</button></div></div>;
}

createRoot(document.getElementById('root')!).render(<React.StrictMode><App /></React.StrictMode>);
