import React from "react";

const Sidebar = ({ username, UID }) => {
  return (
    <div className="sidebar">
      {/* Titolo */}
      <div style={{ marginBottom: "20px" }}>
        <h2>Budget Tracker</h2>
      </div>

      <hr></hr>

      {/* Messaggio di benvenuto */}
      <div style={{ marginBottom: "20px" }}>
        <p>
          Benvenuto <strong>{username}</strong> nella tua zona privata.
        </p>
        <p>
          Sei il <strong>{UID}</strong>* utente sulla piattaforma!
        </p>
      </div>

      <hr></hr>

      {/* Link di navigazione */}
      <nav>
        <ul style={{ listStyle: "none", padding: 0 }}>
          <li style={{ marginBottom: "10px" }}>
            <a href="/profilo">Profilo</a>
          </li>
          <li style={{ marginBottom: "10px" }}>
            <a href="/categorie">Categoria</a>
          </li>
          <li>
            <a href="/report">Report</a>
          </li>
          <li>
            <a href="/">Logout</a>
          </li>
        </ul>
      </nav>
    </div>
  );
};

export default Sidebar;
