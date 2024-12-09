import React from "react";

const Sidebar = ({ username, UID, Pagina }) => {
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
          Benvenuto nella pagina <strong>{Pagina}</strong>, utente{" "}
          <strong>{username}</strong>.
        </p>
        <p>
          Sei il <strong>{UID}</strong>° utente sulla piattaforma!
        </p>
      </div>

      <hr></hr>

      {/* Link di navigazione */}
      <nav>
        <ul style={{ listStyle: "none", padding: 0 }}>
          <li>
            <a href="/Home">Home</a>
          </li>
          <li style={{ marginBottom: "10px" }}>
            <a href="/Profilo">Profilo</a>
          </li>
          <li>
            <a href="/Categorie">Categorie</a>
          </li>
          <li>
            <a href="/Report">Report</a>
          </li>
          <li>
            <a href="/">Logout</a>
          </li>
        </ul>

        <hr></hr>
        <hr></hr>
        <hr></hr>

        <p>©2024 ICT WEB2</p>
      </nav>
    </div>
  );
};

export default Sidebar;
