import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../../components/modal/modal";

function EditMilestone({ milestone, onUpdate, onClose, show }) {
  const [formData, setFormData] = useState({
    MILESTONE_Nome: milestone.MILESTONE_Nome,
    MILESTONE_Descrizione: milestone.MILESTONE_Descrizione,
    MILESTONE_Completata: milestone.MILESTONE_Completata,
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    const baseurl = process.env.REACT_APP_BASE_URL;
    const utenteId = localStorage.getItem("UTENTE_ID");

    try {
      const updateData = new FormData();
      updateData.append("MILESTONE_ID", milestone.MILESTONE_ID);
      updateData.append("UTENTE_ID", utenteId);
      updateData.append("MILESTONE_Completata", milestone.MILESTONE_Completata);
      Object.keys(formData).forEach((key) =>
        updateData.append(key, formData[key])
      );

      const response = await axios.post(
        `${baseurl}/milestone/PUT_Milestone.php`,
        updateData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 200) {
        onUpdate({ ...milestone, ...formData });
        onClose();
      }
    } catch (error) {
      console.error("Errore nell'aggiornamento della milestone:", error);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  return (
    <Modal show={show} onClose={onClose} title="Modifica Milestone">
      <form onSubmit={handleSubmit}>
        <div className="modal-form-group">
          <label>Nome Milestone</label>
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
          <label>
            Completata:
            <input
              type="checkbox"
              name="MILESTONE_Completata"
              checked={formData.MILESTONE_Completata}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  MILESTONE_Completata: e.target.checked,
                })
              }
            />
          </label>
        </div>
        <button type="submit">Salva Modifiche</button>
      </form>
    </Modal>
  );
}

export default EditMilestone;
