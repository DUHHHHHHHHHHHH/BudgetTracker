import React, { useEffect, useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

const Sidebar = ({ username, UID, Pagina }) => {
  const [categorie, setCategorie] = useState([]); // Stato per le categorie
  const [categoriaSelezionata, setCategoriaSelezionata] = useState(null); // Stato per la categoria selezionata

  const navigate = useNavigate();

  useEffect(() => {
    if (Pagina === "Categorie") {
      const baseurl = process.env.REACT_APP_BASE_URL;

      // Recupera l'ID utente dal localStorage
      const utenteId = localStorage.getItem("UTENTE_ID");
      if (!utenteId) {
        console.error("UTENTE_ID non trovato nel localStorage");
        return;
      }

      const formData = new FormData();
      formData.append("UTENTE_ID", utenteId);

      // Effettua la chiamata API per ottenere le categorie
      axios
        .post(`${baseurl}/categoria/GET_Categorie.php`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          console.log("Risultati API Categorie:", res.data);
          setCategorie(Array.isArray(res.data) ? res.data : []); // Verifica che res.data sia un array prima di impostarlo

          console.log("Categorie:", categorie);
        })
        .catch((error) => {
          console.error("Errore nel recupero delle categorie:", error);
          setCategorie([]); // Imposta un array vuoto in caso di errore
        });
    }
  }, [Pagina]);

  const handleSelezionaCategoria = (e) => {
    const categoriaId = e.target.value;

    if (!categoriaId) {
      setCategoriaSelezionata(null); // Nessuna categoria selezionata
      return;
    }

    // Trova la categoria selezionata nell'elenco
    const categoria = categorie.find(
      (cat) => cat.CATEGORIA_ID === parseInt(categoriaId)
    );

    console.log("Categoria selezionata:", categoria);

    setCategoriaSelezionata(categoria); // Salva i dettagli della categoria selezionata
  };

  return (
    <div className="sidebar">
      {/* Titolo */}
      <div style={{ marginBottom: "20px" }}>
        <h2>Budget Tracker</h2>
      </div>

      <hr />

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

      <hr />

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

        <hr />
        <hr />
        <hr />

        {"QTA° " + categorie.length + " categorie create"}
        {Pagina === "Categorie" && Array.isArray(categorie) && (
          <select
            style={{
              padding: "8px",
              borderRadius: "4px",
              border: "1px solid #ccc",
              width: "200px",
            }}
            onChange={handleSelezionaCategoria}
          >
            <option value="">Seleziona una categoria</option>
            {categorie.map((categoria) => (
              <option
                key={categoria.CATEGORIA_ID}
                value={categoria.CATEGORIA_ID}
              >
                {categoria.CATEGORIA_Nome} ({categoria.TIPOLOGIA_Nome})
              </option>
            ))}
          </select>
        )}

        <p>©2024 ICT WEB2</p>
      </nav>
    </div>
  );
};

export default Sidebar;
