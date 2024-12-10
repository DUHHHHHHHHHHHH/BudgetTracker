import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import AddCategoria from "./AddCategoria/AddCategoria";
import EditCategoria from "./AddCategoria/EditCategoria";
import Modal from "../../components/modal/modal";
import Sidebar from "../../components/sidebar/sidebar";

function Categoria() {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [utenteId, setUtenteId] = useState(0);
  const [categorie, setCategorie] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showAddCategoria, setShowAddCategoria] = useState(false);
  const [selectedCategoria, setSelectedCategoria] = useState(null);
  const [showEditModal, setShowEditModal] = useState(false);

  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      navigate("/login");
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, [navigate]);

  const handleDeleteCategoria = async (categoriaId) => {
    try {
      const baseurl = process.env.REACT_APP_BASE_URL;
      const formData = new FormData();
      formData.append("CATEGORIA_ID", categoriaId);
      formData.append("UTENTE_ID", utenteId);

      await axios.post(`${baseurl}/categoria/DELETE_Categoria.php`, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      setCategorie((prevCategorie) =>
        prevCategorie.filter((c) => c.CATEGORIA_ID !== categoriaId)
      );
    } catch (error) {
      console.error("Errore durante l'eliminazione della categoria:", error);
    }
  };

  const handleEditClick = (categoria) => {
    setSelectedCategoria(categoria);
    setShowEditModal(true);
  };

  const handleUpdate = (updatedCategoria) => {
    setCategorie((prevCategorie) =>
      prevCategorie.map((c) =>
        c.CATEGORIA_ID === updatedCategoria.CATEGORIA_ID ? updatedCategoria : c
      )
    );
    setShowEditModal(false);
  };

  useEffect(() => {
    const fetchCategorie = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const formData = new FormData();
        formData.append("UTENTE_ID", utenteId);

        const response = await axios.post(
          `${baseurl}/categoria/GET_Categorie.php`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );

        console.log("Categorie:", response.data);

        const categorieArray = Array.isArray(response.data)
          ? response.data.map((categoria) => ({
              CATEGORIA_ID: categoria.CATEGORIA_ID,
              CATEGORIA_Nome: categoria.CATEGORIA_Nome,
              CATEGORIA_Descrizione: categoria.CATEGORIA_Descrizione,
              CATEGORIA_Budget: categoria.CATEGORIA_Budget,
              TIPOLOGIA_Nome: categoria.TIPOLOGIA_Nome,
              UTENTE_FK_ID: categoria.UTENTE_FK_ID,
            }))
          : [];
        setCategorie(categorieArray);
        setLoading(false);
      } catch (error) {
        console.error("Error fetching categories:", error);
        setCategorie([]);
        setLoading(false);
      }
    };
    if (utenteId) {
      fetchCategorie();
    }
  }, [utenteId]);

  if (loading) {
    return (
      <div
        style={{
          position: "relative",
          top: "50%",
          left: "50%",
          transform: "translate(-50%, -50%)",
          textAlign: "center",
          fontSize: "1.5rem",
        }}
      >
        Loading...
      </div>
    );
  }

  if (categorie.length === 0) {
    return (
      <div style={{ display: "flex", width: "100%" }}>
        <Sidebar username={username} UID={utenteId} Pagina="Categoria" />
        <div className="container" style={{ backgroundColor: "#566a4f" }}>
          <div
            style={{
              textAlign: "center",
              fontSize: "1.5rem",
              marginTop: "20px",
            }}
          >
            Nessuna categoria trovata
            <div style={{ marginTop: "20px" }}>
              <AddCategoria
                onClose={() => setShowAddCategoria(false)}
                utenteId={utenteId}
              />
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div>
      <div style={{ display: "flex", width: "100%" }}>
        <Sidebar username={username} UID={utenteId} Pagina="Categoria" />
        <div className="container" style={{ backgroundColor: "#566a4f" }}>
          <h2 style={{ width: "100%", textAlign: "center" }}>
            Categorie create dall'Utente
          </h2>
          <div
            style={{
              width: "auto",
              overflowX: "auto",
              overflowY: "auto",
              maxHeight: "250px",
              textAlign: "center",
              padding: "10px",
              display: "block ruby",
            }}
          >
            <table className="table table-striped" style={{ width: "auto" }}>
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Descrizione</th>
                  <th>Budget</th>
                  <th>Tipologia Allegata</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                {categorie.map((categoria) => (
                  <tr key={categoria.CATEGORIA_ID}>
                    <td>{categoria.CATEGORIA_Nome}</td>
                    <td>{categoria.CATEGORIA_Descrizione}</td>
                    <td>{categoria.CATEGORIA_Budget}€</td>
                    <td>{categoria.TIPOLOGIA_Nome}</td>
                    <td>
                      <button
                        onClick={() => handleEditClick(categoria)}
                        style={{
                          backgroundColor: "#4CAF50",
                          color: "white",
                          padding: "8px 12px",
                          border: "none",
                          borderRadius: "4px",
                          cursor: "pointer",
                          marginRight: "5px",
                          transition: "background-color 0.3s ease",
                        }}
                      >
                        ✎
                      </button>
                      <button
                        onClick={() =>
                          handleDeleteCategoria(categoria.CATEGORIA_ID)
                        }
                        style={{
                          backgroundColor: "#f44336",
                          color: "white",
                          padding: "8px 12px",
                          border: "none",
                          borderRadius: "4px",
                          cursor: "pointer",
                          transition: "background-color 0.3s ease",
                        }}
                      >
                        🗑
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div style={{ marginTop: "20px", textAlign: "center" }}>
            <h2>Aggiungi una nuova categoria</h2>
            <AddCategoria
              onClose={() => setShowAddCategoria(false)}
              utenteId={utenteId}
            />
          </div>

          {showEditModal && selectedCategoria && (
            <EditCategoria
              categoria={selectedCategoria}
              onUpdate={handleUpdate}
              onClose={() => setShowEditModal(false)}
              show={showEditModal}
            />
          )}
        </div>
      </div>
    </div>
  );
}

export default Categoria;
