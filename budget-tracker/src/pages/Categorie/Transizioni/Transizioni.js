import React, { useEffect, useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

import AddTransizione from "./AddTransizione/AddTransizione";
import EditTransizione from "./AddTransizione/EditTransizione";

function Transizioni({
  boolSelected,
  categoriaNome,
  categoriaid,
  utenteId,
  onTransizioniUpdate,
}) {
  const [transactions, setTransactions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedTransizione, setSelectedTransizione] = useState(null);

  const navigate = useNavigate();

  // Funzione per eliminare una transizione
  const handleDeleteTransizione = async (transizioneId) => {
    try {
      const baseurl = process.env.REACT_APP_BASE_URL;
      const formData = new FormData();
      formData.append("TRANSIZIONE_ID", transizioneId);
      formData.append("UTENTE_ID", utenteId);
      formData.append("CATEGORIA_ID", categoriaid);

      await axios.post(
        `${baseurl}/transizione/DELETE_Transizione.php`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      setTransactions((prevTransactions) =>
        prevTransactions.filter((t) => t.TRANSIZIONE_ID !== transizioneId)
      );

      onTransizioniUpdate(
        transactions.filter((t) => t.TRANSIZIONE_ID !== transizioneId)
      );
    } catch (error) {
      console.error("Errore durante l'eliminazione della transizione:", error);
    }
  };

  // Funzione per aprire il modal di modifica
  const handleEditClick = (transizione) => {
    setSelectedTransizione(transizione);
    setShowEditModal(true);
  };

  // Funzione per aggiornare una transizione dopo la modifica
  const handleUpdate = (updatedTransizione) => {
    setTransactions((prevTransactions) =>
      prevTransactions.map((t) =>
        t.TRANSIZIONE_ID === updatedTransizione.TRANSIZIONE_ID
          ? updatedTransizione
          : t
      )
    );
    onTransizioniUpdate(transactions);
    setShowEditModal(false);
  };

  // Effettua la fetch delle transizioni
  useEffect(() => {
    const fetchTransactions = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const utenteIdLocal = utenteId || localStorage.getItem("UTENTE_ID");

        if (categoriaNome === "default-dev") {
          setLoading(false);
          return;
        }

        if (!boolSelected) {
          setLoading(false);
          return;
        }

        const formData = new FormData();
        formData.append("UTENTE_ID", utenteIdLocal);
        formData.append("CATEGORIA_Nome", categoriaNome);
        formData.append("flagTipoGet", 1);

        const response = await axios.post(
          `${baseurl}/transizione/GET_Transizioni.php`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );

        const transactionArray = Array.isArray(response.data)
          ? response.data.map((transaction) => ({
              TRANSIZIONE_ID: transaction.TRANSIZIONE_ID,
              TRANSIZIONE_Nome: transaction.TRANSIZIONE_Nome,
              TRANSIZIONE_Data: transaction.TRANSIZIONE_Data,
              TRANSIZIONE_DataGenerazione:
                transaction.TRANSIZIONE_DataGenerazione,
              TRANSIZIONE_QTA: transaction.TRANSIZIONE_QTA,
              TRANSIZIONE_Tipo: transaction.TRANSIZIONE_Tipo,
              UTENTE_FK_ID: transaction.UTENTE_FK_ID,
              CATEGORIA_FK_ID: transaction.CATEGORIA_FK_ID,
            }))
          : [];
        setTransactions(transactionArray);
        onTransizioniUpdate(transactionArray);
        setLoading(false);
      } catch (error) {
        console.error("Error fetching transactions:", error);
        setTransactions([]);
        onTransizioniUpdate([]);
        setLoading(false);
      }
    };

    if (boolSelected && categoriaNome) {
      fetchTransactions();
    }
  }, [boolSelected, categoriaNome, utenteId, onTransizioniUpdate]);

  if (loading || categoriaNome === "default-dev") {
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
        Selezionare una categoria per continuare.
      </div>
    );
  }

  if (transactions.length === 0 && categoriaNome !== "default-dev") {
    return (
      <>
        <div style={{ width: "100%", textAlign: "center", marginTop: "25%" }}>
          <div
            style={{
              textAlign: "center",
              fontSize: "1.5rem",
              marginTop: "20px",
            }}
          >
            Nessuna transizione trovata per la categoria
            <h5>
              <strong>{categoriaNome}</strong>.
            </h5>
          </div>
          <div style={{ marginTop: "20px" }}>
            <AddTransizione
              CATEGORIA_Nome={categoriaNome}
              onTransizioniUpdate={onTransizioniUpdate}
            />
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <h2 style={{ width: "100%", textAlign: "center" }}>
        Transizioni per {categoriaNome}.
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
              <th>Data</th>
              <th>Quantità</th>
              <th>Tipo</th>
              <th>Data Generazione</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            {transactions.map((transaction) => (
              <tr key={transaction.TRANSIZIONE_ID}>
                <td>{transaction.TRANSIZIONE_Nome}</td>
                <td>{transaction.TRANSIZIONE_Data.split(" ")[0]}</td>
                <td>{transaction.TRANSIZIONE_QTA}€</td>
                <td>{transaction.TRANSIZIONE_Tipo}</td>
                <td style={{ fontSize: "0.75em" }}>
                  {transaction.TRANSIZIONE_DataGenerazione}
                </td>
                <td>
                  <button
                    onClick={() => handleEditClick(transaction)}
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
                      handleDeleteTransizione(transaction.TRANSIZIONE_ID)
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
        <h2>Aggiungi una nuova transizione</h2>
        <AddTransizione
          CATEGORIA_Nome={categoriaNome}
          onTransizioniUpdate={onTransizioniUpdate}
        />
      </div>

      {showEditModal && selectedTransizione && (
        <EditTransizione
          transizione={selectedTransizione}
          onUpdate={handleUpdate}
          onClose={() => setShowEditModal(false)}
          show={showEditModal}
        />
      )}
    </>
  );
}

export default Transizioni;
