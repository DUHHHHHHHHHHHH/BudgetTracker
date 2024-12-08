import React, { useState, useEffect } from "react";
import axios from "axios";
import Modal from "../../../components/modal/modal";

function AddCategoria({ onClose, utenteId }) {
  const [formData, setFormData] = useState({
    CATEGORIA_Nome: "",
    CATEGORIA_Descrizione: "",
    CATEGORIA_Budget: "",
    nomeTipologiaAllegata: "",
  });

  const [tipologie, setTipologie] = useState([]);

  useEffect(() => {
    const fetchTipologie = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const response = await axios.get(
          `${baseurl}/tipologie/GET_Tipologie.php`
        );
        setTipologie(response.data || []);
      } catch (error) {
        console.error("Error fetching tipologie:", error);
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

      if (response.data.code === 201) {
        onClose(); // Chiude la modale dopo il successo
      }
    } catch (error) {
      console.error("Error adding categoria:", error);
    }
  };

  return (
    <div>
      <h3>Aggiungi Categoria</h3>
      <form onSubmit={handleSubmit}>
        <div>
          <label>Nome Categoria</label>
          <input
            type="text"
            name="CATEGORIA_Nome"
            value={formData.CATEGORIA_Nome}
            onChange={handleChange}
            required
          />
        </div>
        <div>
          <label>Descrizione</label>
          <input
            type="text"
            name="CATEGORIA_Descrizione"
            value={formData.CATEGORIA_Descrizione}
            onChange={handleChange}
            required
          />
        </div>
        <div>
          <label>Budget</label>
          <input
            type="number"
            name="CATEGORIA_Budget"
            value={formData.CATEGORIA_Budget}
            onChange={handleChange}
            required
          />
        </div>
        <div>
          <label>Tipologia Allegata</label>
          <select
            name="nomeTipologiaAllegata"
            value={formData.nomeTipologiaAllegata}
            onChange={handleChange}
            required
          >
            {tipologie.map((tipologia) => (
              <option
                key={tipologia.TIPOLOGIA_ID}
                value={tipologia.TIPOLOGIA_Nome}
              >
                {tipologia.TIPOLOGIA_Nome}
              </option>
            ))}
          </select>
        </div>
        <button type="submit">Salva Categoria</button>
      </form>
    </div>
  );
}

export default AddCategoria;
