import React, { useEffect, useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import Spacer from "../misc/spacer/spacer";

const Sidebar = ({ username, UID, Pagina, onCategoriaSelect }) => {
  const [categorie, setCategorie] = useState([]); // Stato per le categorie
  const [categoriaSelezionata, setCategoriaSelezionata] = useState(null); // Stato per la categoria selezionata

  const navigate = useNavigate();

  useEffect(() => {
    if (Pagina === "Gestionale" || Pagina === "Report") {
      // le pagine che necessitano le categorie da visualizzare

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

          // console.log("Categorie:", categorie);
        })
        .catch((error) => {
          console.error("Errore nel recupero delle categorie:", error);
          setCategorie([]); // Imposta un array vuoto in caso di errore
        });
    }
  }, [Pagina]);

  const handleSelezionaCategoria = (e) => {
    const categoriaNome = e.target.value; // Ottieni il nome della categoria
    const utenteId = localStorage.getItem("UTENTE_ID");

    if (categoriaNome === "") {
      setCategoriaSelezionata(null);
      onCategoriaSelect(utenteId, "default-dev", true, null, 0); // Modificato per inviare il valore di default
      return;
    }

    // Trova la categoria selezionata nell'elenco
    const categoria = categorie.find((cat) => {
      console.log("Confronto:", cat.CATEGORIA_Nome, categoriaNome); // Debug
      return cat.CATEGORIA_Nome === categoriaNome;
    });

    console.log("Categorie disponibili:", categorie); // Debug
    console.log("Categoria selezionata:", categoria);

    //console.log("categoria budget:", categoria.CATEGORIA_Budget);
    // console.log("categoria id:", categoria.CATEGORIA_ID);

    setCategoriaSelezionata(categoria);
    onCategoriaSelect(
      utenteId,
      categoria.CATEGORIA_Nome,
      true,
      categoria.CATEGORIA_ID,
      categoria.CATEGORIA_Budget
    ); // Aggiunto CATEGORIA_ID
  };

  return (
    <div className="sidebar">
      {/* Titolo */}

      <div
        style={{
          marginBottom: "0px",
          display: "flex",
          justifyContent: "center",
        }}
      >
        <img
          src={process.env.PUBLIC_URL + "/disgustoso.png"}
          alt="Logo"
          style={{ width: "100px", height: "100px" }}
        />
        {/* <div style={{ textAlign: "center" }}>Disgustoso! Budget Tracker</div> */}
      </div>

      {/* Messaggio di benvenuto */}
      <div style={{ marginBottom: "20px" }}>
        <p>
          <div style={{ textAlign: "center" }}>
            <strong>{username}</strong> benvenuto in <strong>{Pagina}</strong>
          </div>
          {/*N{UID}!*/}{" "}
          <div style={{ textAlign: "center", marginTop: "10px" }}>
            Siamo felici di averti qui come N°<strong>{UID}</strong> utente!.
          </div>
        </p>
      </div>

      <hr />
      <hr />
      <hr />

      {/* Link di navigazione */}
      <nav>
        <ul style={{ listStyle: "none", padding: 0 }}>
          <li>
            <a href="/Home">Home</a>
          </li>
          <li style={{}}>
            <a href="/Profilo">Profilo</a>
          </li>
          <li>
            <a href="/Categoria">Categoria</a>
          </li>
          <li>
            <a href="/Gestionale">Gestionale</a>
          </li>
          <li>
            <a href="/Report">Report</a>
          </li>
          <li>
            <a href="/">Logout</a>
          </li>
        </ul>

        <hr />
        <div style={{ textAlign: "center", marginTop: "25px" }}>
          {(Pagina === "Gestionale" || Pagina === "Report") &&
            Array.isArray(categorie) && (
              <>
                <div
                  style={{
                    textAlign: "center",
                    marginBottom: "10px",
                    marginTop: "10px",
                  }}
                >
                  {`trovate ${categorie.length}* categorie`}
                </div>
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
                      value={categoria.CATEGORIA_Nome}
                    >
                      {categoria.CATEGORIA_Nome} ({categoria.TIPOLOGIA_Nome})
                    </option>
                  ))}
                </select>
              </>
            )}
        </div>

        <div style={{ textAlign: "center", marginTop: "25px" }}>
          <hr />
          <hr />
          <hr />

          <p>©2024 ICT WEB2, BTD!</p>

          <hr />
          <hr />
          <hr />
        </div>
      </nav>
    </div>
  );
};

export default Sidebar;
