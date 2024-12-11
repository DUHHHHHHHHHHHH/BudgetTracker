import React, { useEffect, useState } from "react";
import axios from "axios";
import AddMilestone from "./AddMilestone/AddMilestones";
import EditMilestone from "./AddMilestone/EditMilestones";

import { FaTrash } from "react-icons/fa";

function Milestones({
  boolSelected,
  categoriaNome,
  categoriaid,
  utenteId,
  onMilestonesUpdate,
}) {
  const [milestones, setMilestones] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedMilestone, setSelectedMilestone] = useState(null);

  // Fetch delle milestones
  useEffect(() => {
    const fetchMilestones = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const utenteIdLocal = utenteId || localStorage.getItem("UTENTE_ID");

        if (!boolSelected || categoriaNome === "default-dev") {
          setLoading(false);
          return;
        }

        const formData = new FormData();
        formData.append("UTENTE_ID", utenteIdLocal);
        formData.append("CATEGORIA_Nome", categoriaNome);
        formData.append("flagTipoGet", "1");

        const response = await axios.post(
          `${baseurl}/milestone/GET_Milestones.php`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );

        const milestonesData = Array.isArray(response.data)
          ? response.data
          : [];
        setMilestones(milestonesData);
        onMilestonesUpdate(milestonesData);
        setLoading(false);
      } catch (error) {
        console.error("Errore nel fetch delle milestones:", error);
        setMilestones([]);
        onMilestonesUpdate([]);
        setLoading(false);
      }
    };

    if (boolSelected && categoriaNome) {
      fetchMilestones();
    }
  }, [boolSelected, categoriaNome, utenteId, onMilestonesUpdate]);

  // Elimina milestone
  const handleDeleteMilestone = async (milestoneId) => {
    try {
      const baseurl = process.env.REACT_APP_BASE_URL;
      const formData = new FormData();
      formData.append("MILESTONE_ID", milestoneId);
      formData.append("UTENTE_ID", utenteId);

      await axios.post(`${baseurl}/milestone/DELETE_Milestone.php`, formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });

      const updatedMilestones = milestones.filter(
        (m) => m.MILESTONE_ID !== milestoneId
      );
      setMilestones(updatedMilestones);
      onMilestonesUpdate(updatedMilestones);
    } catch (error) {
      console.error("Errore durante l'eliminazione della milestone:", error);
    }
  };

  // Gestione apertura modal modifica
  const handleEditClick = (milestone) => {
    setSelectedMilestone(milestone);
    setShowEditModal(true);
  };

  // Aggiorna milestone dopo modifica
  const handleUpdate = (updatedMilestone) => {
    setMilestones((prevMilestones) =>
      prevMilestones.map((m) =>
        m.MILESTONE_ID === updatedMilestone.MILESTONE_ID ? updatedMilestone : m
      )
    );
    onMilestonesUpdate(milestones);
    setShowEditModal(false);
  };

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
  if (!Array.isArray(milestones) || milestones.length === 0) {
    return (
      <div style={{ width: "100%", textAlign: "center", marginTop: "25%" }}>
        <div
          style={{
            textAlign: "center",
            fontSize: "1.5rem",
            marginTop: "20px",
          }}
        >
          Nessuna milestone trovata per la categoria
          <h5>
            <strong>{categoriaNome}</strong>.
          </h5>
        </div>
        <div style={{ marginTop: "20px" }}>
          <AddMilestone
            categoriaNome={categoriaNome}
            onMilestonesUpdate={onMilestonesUpdate}
          />
        </div>
      </div>
    );
  }

  return (
    <>
      <h2 style={{ width: "100%", textAlign: "center" }}>
        Milestones per {categoriaNome}
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
              <th>Inizio</th>
              <th>Fine</th>
              <th>Completata</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            {milestones.map((milestone) => (
              <tr key={milestone.MILESTONE_ID}>
                <td>{milestone.MILESTONE_Nome}</td>
                <td>
                  <div
                    style={{
                      maxWidth: "200px",
                      maxHeight: "100px",
                      overflowX: "auto",
                      overflowY: "auto",
                      padding: "5px",
                    }}
                  >
                    {milestone.MILESTONE_Descrizione}
                  </div>
                </td>
                <td>{milestone.MILESTONE_DataInizio}</td>
                <td>{milestone.MILESTONE_DataFine}</td>
                <td>
                  <div
                    style={{
                      width: "20px",
                      height: "20px",
                      backgroundColor: milestone.MILESTONE_Completata
                        ? "#4CAF50"
                        : "#f44336",
                      margin: "0 auto",
                      borderRadius: "3px",
                    }}
                  />
                </td>
                <td>
                  <button
                    onClick={() => handleEditClick(milestone)}
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
                      handleDeleteMilestone(milestone.MILESTONE_ID)
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
                    <FaTrash />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div style={{ marginTop: "20px", textAlign: "center" }}>
        <h2>Aggiungi una nuova milestone</h2>
        <AddMilestone
          categoriaNome={categoriaNome}
          onMilestonesUpdate={onMilestonesUpdate}
        />
      </div>

      {showEditModal && selectedMilestone && (
        <EditMilestone
          milestone={selectedMilestone}
          onUpdate={handleUpdate}
          onClose={() => setShowEditModal(false)}
          show={showEditModal}
        />
      )}
    </>
  );
}

export default Milestones;
