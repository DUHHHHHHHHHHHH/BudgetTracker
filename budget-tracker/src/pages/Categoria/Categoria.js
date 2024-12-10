import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import AddCategoria from "./AddCategoria/AddCategoria";
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

  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      navigate("/login");
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, [navigate]);

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
        setCategorie(response.data || []);
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

  const handleShowAddCategoria = () => setShowAddCategoria(true);
  const handleCloseAddCategoria = () => setShowAddCategoria(false);

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

      setCategorie(categorie.filter((c) => c.CATEGORIA_ID !== categoriaId));
    } catch (error) {
      console.error("Errore durante l'eliminazione della categoria:", error);
    }
  };

  const handleEditClick = (categoria) => {
    setSelectedCategoria(categoria);
    // Add your edit logic here
  };

  if (loading) {
    return <div>Loading...</div>;
  }

  const renderContent = () => {
    if (categorie.length === 0) {
      return (
        <div style={{ textAlign: "center" }}>
          <h3>Nessuna categoria trovata</h3>
          <button onClick={handleShowAddCategoria}>Aggiungi Categoria</button>
        </div>
      );
    }

    return (
      <div className="container">
        <div className="containerForB">
          <h2>Categorie create dall'Utente</h2>
          <div className="containerForBB">
            <table>
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Descrizione</th>
                  <th>Budget</th>
                  <th>Tipologia Allegata</th>
                </tr>
              </thead>
              <tbody>
                {categorie.map((categoria) => (
                  <tr key={categoria.CATEGORIA_ID}>
                    <td>{categoria.CATEGORIA_Nome}</td>
                    <td>{categoria.CATEGORIA_Descrizione}</td>
                    <td>{categoria.CATEGORIA_Budget}</td>
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
        </div>

        <Modal show={showAddCategoria} onClose={handleCloseAddCategoria}>
          <AddCategoria onClose={handleCloseAddCategoria} utenteId={utenteId} />
        </Modal>
      </div>
    );
  };

  return (
    <div>
      <div style={{ display: "flex", width: "100%" }}>
        <Sidebar username={username} UID={utenteId} Pagina="Categoria" />
        {renderContent()}
      </div>
    </div>
  );
}

export default Categoria;
