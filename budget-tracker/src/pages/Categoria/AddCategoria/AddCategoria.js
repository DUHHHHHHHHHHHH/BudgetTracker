import React, { useState, useEffect } from "react";
import axios from "axios";
import Modal from "../../../components/modal/modal";

function AddCategoria({ onClose, utenteId, onAddCategoria }) {
  const [show, setShow] = useState(false);
  const [error, setError] = useState("");
  const [formData, setFormData] = useState({
    CATEGORIA_Nome: "",
    CATEGORIA_Descrizione: "",
    CATEGORIA_Budget: "",
    nomeTipologiaAllegata: "",
  });

  const [tipologie, setTipologie] = useState([]);

  const handleClose = () => {
    setShow(false);
    setError("");
    onClose();
  };
  const handleShow = () => setShow(true);

  useEffect(() => {
    const fetchTipologie = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const response = await axios.get(
          `${baseurl}/tipologia/GET_Tipologie.php`
        );
        setTipologie(response.data || []);
        console.log(response.data);
      } catch (error) {
        console.error("Error fetching tipologie:", error);
        setError("Errore nel caricamento delle tipologie");
      }
    };
    fetchTipologie();
  }, []);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const baseurl = process.env.REACT_APP_BASE_URL;

    try {
      const postData = new FormData();
      postData.append("UTENTE_ID", utenteId);
      postData.append("CATEGORIA_Nome", formData.CATEGORIA_Nome);
      postData.append("CATEGORIA_Descrizione", formData.CATEGORIA_Descrizione);
      postData.append("CATEGORIA_Budget", formData.CATEGORIA_Budget);
      postData.append("nomeTipologiaAllegata", formData.nomeTipologiaAllegata);

      const response = await axios.post(
        `${baseurl}/categoria/POST_Categoria.php`,
        postData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 200) {
        console.log("response data: ", response.data);
        const nuovaCategoria = {
          CATEGORIA_ID: response.data.CATEGORIA_ID, // Supponendo che l'ID venga restituito dal server
          CATEGORIA_Nome: formData.CATEGORIA_Nome,
          CATEGORIA_Descrizione: formData.CATEGORIA_Descrizione,
          CATEGORIA_Budget: formData.CATEGORIA_Budget,
          TIPOLOGIA_Nome: formData.nomeTipologiaAllegata,
        };
        onAddCategoria(nuovaCategoria);
        handleClose();
      }

      if (response.data.code === 500) {
        setError("La categoria esiste già col nome inserito");
      }
    } catch (error) {
      console.error("Errore nell'aggiungere la categoria :", error);
      setError(
        "Errore! Categoria con Tipologia Allegata già esistente, usare un altro nome"
      );
    }
  };

  return (
    <div className="modal-container">
      <button
        className="modal-btn-primary"
        onClick={handleShow}
        style={{ backgroundColor: "#3b3b3b" }}
      >
        +
      </button>

      <Modal
        show={show}
        onClose={handleClose}
        title="Aggiungi Categoria"
        error={error}
      >
        <form onSubmit={handleSubmit}>
          <div className="modal-form-group">
            <label>Nome Categoria</label>
            <input
              type="text"
              name="CATEGORIA_Nome"
              value={formData.CATEGORIA_Nome}
              onChange={handleChange}
              required
              autoComplete="off"
            />
          </div>
          <div className="modal-form-group">
            <label>Descrizione</label>
            <input
              type="text"
              name="CATEGORIA_Descrizione"
              value={formData.CATEGORIA_Descrizione}
              onChange={handleChange}
              required
              autoComplete="off"
            />
          </div>
          <div className="modal-form-group">
            <label>Budget</label>
            <input
              type="number"
              name="CATEGORIA_Budget"
              value={formData.CATEGORIA_Budget}
              onChange={handleChange}
              required
              autoComplete="off"
            />
          </div>
          <div className="modal-form-group">
            <label>Tipologia Allegata</label>

            <select
              name="nomeTipologiaAllegata"
              value={formData.nomeTipologiaAllegata}
              onChange={handleChange}
              required
            >
              <option value="">Seleziona una tipologia</option>
              {tipologie && tipologie.length > 0 ? (
                tipologie.map((tipologia) => (
                  <>
                    <option
                      key={tipologia.TIPOLOGIA_ID}
                      value={tipologia.TIPOLOGIA_Nome}
                    >
                      {tipologia.TIPOLOGIA_Nome}
                    </option>
                  </>
                ))
              ) : (
                <option value="" disabled>
                  Nessuna tipologia disponibile
                </option>
              )}
            </select>
            {/* devo mostrare la descrizione della tipologia che ho selezionato */}
            {formData.nomeTipologiaAllegata && (
              <p style={{ fontSize: "12px" }}>
                {
                  tipologie.find(
                    (tipologia) =>
                      tipologia.TIPOLOGIA_Nome ===
                      formData.nomeTipologiaAllegata
                  )?.TIPOLOGIA_Descrizione
                }
              </p>
            )}
          </div>
          <button type="submit">Salva Categoria</button>
        </form>
      </Modal>
    </div>
  );
}

export default AddCategoria;
