import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import './styles.css';

type Health = {
  status: string;
  service: string;
  version: string;
  database: string;
  science: string;
  time_utc: string;
};

function App() {
  const [health, setHealth] = useState<Health | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetch('http://127.0.0.1:8080/api/v1/health')
      .then((response) => {
        if (!response.ok) throw new Error(`Core returned ${response.status}`);
        return response.json();
      })
      .then(setHealth)
      .catch((reason: Error) => setError(reason.message));
  }, []);

  return (
    <main className="shell">
      <section className="hero">
        <span className="eyebrow">0.1 Alpha · Foundation</span>
        <h1>🌌 AstroHub</h1>
        <p className="lead">Deine offene, lokale Astronomie-Plattform.</p>
        <p className="byline">Developed by Multimedia Zauber</p>
      </section>

      <section className="status-card" aria-live="polite">
        <h2>Systemstatus</h2>
        {!health && !error && <p>AstroHub Core wird geprüft …</p>}
        {error && <p className="offline">Core offline · {error}</p>}
        {health && (
          <dl>
            <div><dt>Core</dt><dd>{health.status}</dd></div>
            <div><dt>SQLite</dt><dd>{health.database}</dd></div>
            <div><dt>Science</dt><dd>{health.science}</dd></div>
            <div><dt>Version</dt><dd>{health.version}</dd></div>
          </dl>
        )}
      </section>

      <footer>Free & Open Source · Local First</footer>
    </main>
  );
}

createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
);
