import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../../components/modal/modal";

function AddMilestone({ categoriaNome, onMilestonesUpdate }) {
  const [show, setShow] = useState(false);
  const [error, setError] = useState("");
  const [formData, setFormData] = useState({
    MILESTONE_Nome: "",
    MILESTONE_Descrizione: "",
    MILESTONE_DataInizio: "",
    MILESTONE_DataFine: "",
  });

  const handleClose = () => {
    setShow(false);
    setError("");
  };
  const handleShow = () => setShow(true);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const baseurl = process.env.REACT_APP_BASE_URL;
    const utenteId = localStorage.getItem("UTENTE_ID");

    const dataInizio = new Date(formData.MILESTONE_DataInizio);
    const dataFine = new Date(formData.MILESTONE_DataFine);

    if (dataInizio > dataFine) {
      setError("La data di inizio non può essere successiva alla data di fine");
      return;
    }

    try {
      const postData = new FormData();
      postData.append("UTENTE_ID", utenteId);
      postData.append("CATEGORIA_Nome", categoriaNome);
      Object.keys(formData).forEach((key) =>
        postData.append(key, formData[key])
      );

      const response = await axios.post(
        `${baseurl}/milestone/POST_Milestone.php`,
        postData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 201) {
        onMilestonesUpdate((prev) => [...prev, response.data.milestone]);
        handleClose();
        setFormData({
          MILESTONE_Nome: "",
          MILESTONE_Descrizione: "",
          MILESTONE_DataInizio: "",
          MILESTONE_DataFine: "",
        });
      }
    } catch (error) {
      console.error("Errore nell'aggiunta della milestone:", error);
      setError("Si è verificato un errore durante l'aggiunta della milestone");
    }
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Calcola le date limite
  const today = new Date();
  const minDate = new Date(
    today.getFullYear() - 10,
    today.getMonth(),
    today.getDate()
  )
    .toISOString()
    .split("T")[0];
  const maxDate = new Date(
    today.getFullYear() + 10,
    today.getMonth(),
    today.getDate()
  )
    .toISOString()
    .split("T")[0];

  return (
    <div className="modal-container">
      <button className="modal-btn-primary" onClick={handleShow}>
        +
      </button>

      <Modal
        show={show}
        onClose={handleClose}
        title={`POST Milestone per ${categoriaNome}`}
        error={error}
      >
        <form onSubmit={handleSubmit}>
          <div className="modal-form-group">
            <label>Nome</label>
            <input
              type="text"
              name="MILESTONE_Nome"
              value={formData.MILESTONE_Nome}
              onChange={handleChange}
              required
            />
          </div>

          <div className="modal-form-group">
            <label>Descrizione</label>
            <textarea
              name="MILESTONE_Descrizione"
              value={formData.MILESTONE_Descrizione}
              onChange={handleChange}
              required
            />
          </div>

          <div className="modal-form-group">
            <label>Data Inizio</label>
            <input
              type="date"
              name="MILESTONE_DataInizio"
              value={formData.MILESTONE_DataInizio}
              onChange={handleChange}
              min={minDate}
              max={formData.MILESTONE_DataFine || maxDate}
              required
            />
          </div>

          <div className="modal-form-group">
            <label>Data Fine</label>
            <input
              type="date"
              name="MILESTONE_DataFine"
              value={formData.MILESTONE_DataFine}
              onChange={handleChange}
              min={formData.MILESTONE_DataInizio || minDate}
              max={maxDate}
              required
            />
          </div>

          <button type="submit">Salva</button>
        </form>
      </Modal>
    </div>
  );
}

export default AddMilestone;
