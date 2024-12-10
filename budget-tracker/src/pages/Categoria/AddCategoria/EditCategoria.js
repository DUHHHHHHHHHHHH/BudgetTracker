import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../components/modal/modal";

function EditCategoria({ categoria, onUpdate, onClose, show }) {
  const [formData, setFormData] = useState({
    CATEGORIA_newNome: categoria.CATEGORIA_Nome || "",
    CATEGORIA_newDescrizione: categoria.CATEGORIA_Descrizione || "",
  });
  const [error, setError] = useState("");

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
      const updateData = new FormData();
      updateData.append("CATEGORIA_ID", categoria.CATEGORIA_ID);
      updateData.append("CATEGORIA_newNome", formData.CATEGORIA_newNome);
      updateData.append(
        "CATEGORIA_newDescrizione",
        formData.CATEGORIA_newDescrizione
      );

      const response = await axios.post(
        `${baseurl}/categoria/PUT_Categoria.php`,
        updateData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 200) {
        onUpdate({ ...categoria, ...formData });
        onClose();
      }
    } catch (error) {
      console.error("Error updating categoria:", error);
      setError(
        "Si è verificato un errore durante l'aggiornamento della categoria."
      );
    }
  };

  return (
    <Modal
      show={show}
      onClose={onClose}
      title="Modifica Categoria"
      error={error}
    >
      <form onSubmit={handleSubmit}>
        <div>
          <label>Nome Categoria</label>
          <input
            type="text"
            name="CATEGORIA_newNome"
            value={formData.CATEGORIA_newNome}
            onChange={handleChange}
            required
          />
        </div>
        <div>
          <label>Descrizione</label>
          <input
            type="text"
            name="CATEGORIA_newDescrizione"
            value={formData.CATEGORIA_newDescrizione}
            onChange={handleChange}
            required
          />
        </div>
        <button type="submit">Salva Modifiche</button>
      </form>
    </Modal>
  );
}

export default EditCategoria;
