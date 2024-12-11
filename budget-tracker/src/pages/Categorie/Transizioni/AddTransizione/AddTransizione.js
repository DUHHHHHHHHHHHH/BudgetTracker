import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../../components/modal/modal";

function AddTransizione({ CATEGORIA_Nome }) {
  const [show, setShow] = useState(false);
  const [error, setError] = useState("");
  const [formData, setFormData] = useState({
    TRANSIZIONE_Nome: "",
    TRANSIZIONE_Data: "",
    TRANSIZIONE_QTA: "",
    TRANSIZIONE_Tipo: "SPESA",
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

    try {
      const postData = new FormData();
      postData.append("UTENTE_ID", utenteId);
      postData.append("CATEGORIA_Nome", CATEGORIA_Nome);
      postData.append("TRANSIZIONE_Nome", formData.TRANSIZIONE_Nome);
      postData.append("TRANSIZIONE_Data", formData.TRANSIZIONE_Data);
      postData.append("TRANSIZIONE_Tipo", formData.TRANSIZIONE_Tipo);
      postData.append("TRANSIZIONE_QTA", formData.TRANSIZIONE_QTA);

      const response = await axios.post(
        `${baseurl}/transizione/POST_Transizione.php`,
        postData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 201) {
        handleClose();
        setFormData({
          TRANSIZIONE_Nome: "",
          TRANSIZIONE_Data: "",
          TRANSIZIONE_QTA: "",
          TRANSIZIONE_Tipo: "SPESA",
        });
      }
    } catch (error) {
      console.error("Error adding transaction:", error);
      setError(
        "Si è verificato un errore durante l'aggiunta della transazione"
      );
    }
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  return (
    <div className="modal-container">
      <button className="modal-btn-primary" onClick={handleShow}>
        +
      </button>

      <Modal
        show={show}
        onClose={handleClose}
        title={`POST Transizione in ${CATEGORIA_Nome}`}
        error={error}
      >
        <form onSubmit={handleSubmit}>
          <div className="modal-form-group">
            <label>Nome Transizione</label>
            <input
              type="text"
              name="TRANSIZIONE_Nome"
              value={formData.TRANSIZIONE_Nome}
              onChange={handleChange}
              required
              autoComplete="off"
            />
          </div>

          <div className="modal-form-group">
            <label>Data</label>
            <input
              type="date"
              name="TRANSIZIONE_Data"
              value={formData.TRANSIZIONE_Data}
              onChange={handleChange}
              min="2020-01-01"
              max={new Date().toISOString().split("T")[0]}
              required
            />
          </div>

          <div className="modal-form-group">
            <label>Quantità</label>
            <input
              type="number"
              name="TRANSIZIONE_QTA"
              value={formData.TRANSIZIONE_QTA}
              onChange={handleChange}
              required
              autoComplete="off"
            />
          </div>

          <div className="modal-form-group">
            <label>Tipo</label>
            <select
              name="TRANSIZIONE_Tipo"
              value={formData.TRANSIZIONE_Tipo}
              onChange={handleChange}
              required
            >
              <option value="SPESA">SPESA</option>
              <option value="ENTRATA">ENTRATA</option>
            </select>
          </div>

          <button type="submit">Salva</button>
        </form>
      </Modal>
    </div>
  );
}
export default AddTransizione;
