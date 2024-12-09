import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../../components/modal/modal";

function EditTransizione({ transizione, onUpdate, onClose, show }) {
  const [formData, setFormData] = useState({
    TRANSIZIONE_Nome: transizione.TRANSIZIONE_Nome || "",
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const baseurl = process.env.REACT_APP_BASE_URL;
    const utenteId = localStorage.getItem("UTENTE_ID");

    try {
      const updateData = new FormData();
      updateData.append("TRANSIZIONE_ID", transizione.TRANSIZIONE_ID);
      updateData.append("UTENTE_ID", utenteId);
      updateData.append("TRANSIZIONE_NewNome", formData.TRANSIZIONE_Nome);

      const response = await axios.post(
        `${baseurl}/transizione/PUT_Transizione.php`,
        updateData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      console.log(response.data);

      if (response.data.code === 200) {
        onUpdate({
          ...transizione,
          TRANSIZIONE_Nome: formData.TRANSIZIONE_Nome,
        });
        onClose();
      }
    } catch (error) {
      console.error("Error updating transaction:", error);
    }
  };

  return (
    <Modal show={show} onClose={onClose} title="Modifica Nome Transizione">
      <form onSubmit={handleSubmit}>
        <div className="modal-form-group">
          <label>Nome Transizione</label>
          <input
            type="text"
            name="TRANSIZIONE_Nome"
            value={formData.TRANSIZIONE_Nome}
            onChange={handleChange}
            required
          />
        </div>

        <button type="submit">Salva Modifiche</button>
      </form>
    </Modal>
  );
}

export default EditTransizione;
