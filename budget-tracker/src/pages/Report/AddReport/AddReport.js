import React, { useState } from "react";
import axios from "axios";
import Modal from "../../../components/modal/modal";

function AddReport({ categoriaNome, categoriaid, utenteId }) {
  const [show, setShow] = useState(false);
  const [error, setError] = useState("");
  const [nome, setNome] = useState("");
  const [descrizione, setDescrizione] = useState("");
  const [loading, setLoading] = useState(false);

  const handleClose = () => {
    setShow(false);
    setError("");
    setNome("");
    setDescrizione("");
  };

  const handleShow = () => setShow(true);

  const generateCSV = async () => {
    try {
      const baseurl = process.env.REACT_APP_BASE_URL;

      const mixFormData = new FormData();
      mixFormData.append("UTENTE_ID", utenteId);
      mixFormData.append("CATEGORIA_Nome", categoriaNome);
      mixFormData.append("flagTipoGet", 1);

      const responseTransizioni = await axios.post(
        `${baseurl}/transizione/GET_Transizioni.php`,
        mixFormData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      const responseMilestones = await axios.post(
        `${baseurl}/milestone/GET_Milestones.php`,
        mixFormData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      const transizioni = responseTransizioni.data || [];
      const milestones = responseMilestones.data || [];

      const currentDate = new Date();
      const formattedDate = currentDate.toISOString().split("T")[0]; // "YYYY-MM-DD"

      // GENERAZIONE DEL CSV
      let csvContent = `NOME REPORT, DESCRIZIONE, DATA GENERAZIONE\n`;
      csvContent += `${nome}, ${descrizione}, ${formattedDate}\n\n\n`;

      const transizioniCount = transizioni.length;
      const milestonesCount = milestones.length;

      if (transizioni.length === 0 && milestones.length === 0) {
        csvContent += "Nessun dato trovato\n\n";
        csvContent += `Transizioni:, ${transizioniCount}\n`;
        csvContent += `Milestones:, ${milestonesCount}\n`;
      } else {
        if (transizioni.length > 0) {
          csvContent += `Transizioni:, ${transizioniCount}\n\n`;
          csvContent += `NOME, DATA, BUDGET, TIPO\n`;

          transizioni.forEach((transizione) => {
            csvContent += `${transizione.TRANSIZIONE_Nome},${transizione.TRANSIZIONE_Data},${transizione.TRANSIZIONE_QTA},${transizione.TRANSIZIONE_Tipo}\n`;
          });
        }

        csvContent += "\n";
        if (milestones.length > 0) {
          csvContent += `Milestones:, ${milestonesCount}\n\n`;
          csvContent += `NOME, DESCRIZIONE, DATA INIZIO, DATA FINE, COMPLETATA\n`;
          milestones.forEach((milestone) => {
            let compl = "NO";
            if (milestone.MILESTONE_Completata) {
              compl = "SI";
            }

            csvContent += `${milestone.MILESTONE_Nome},${milestone.MILESTONE_Descrizione},${milestone.MILESTONE_DataInizio},${milestone.MILESTONE_DataFine}, ${compl}\n`;
          });
        }

        csvContent += "\n\n\n";
        csvContent += "fine file. \n";
      }

      // Create a Blob from the CSV string and generate the file
      const blob = new Blob([csvContent], { type: "text/csv" });
      const file = new File([blob], `${categoriaNome}_report.csv`, {
        type: "text/csv",
      });

      return file; // Return the file to be used in the form submission
    } catch (err) {
      console.error("Errore durante la generazione del CSV:", err);
      setError("Errore durante la generazione del file CSV.");
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!nome || !descrizione) {
      setError("Compila tutti i campi.");
      return;
    }

    try {
      setLoading(true);
      setError("");

      const baseurl = process.env.REACT_APP_BASE_URL;

      const currentDate = new Date();
      const formattedDate = currentDate.toISOString().split("T")[0]; // "YYYY-MM-DD"

      // Crea il file CSV
      const csvFile = await generateCSV();

      const formData = new FormData();
      formData.append("REPORT_Nome", nome);
      formData.append("REPORT_Descrizione", descrizione);
      formData.append("UTENTE_ID", utenteId);
      formData.append("REPORT_DataGenerazione", formattedDate);

      // Aggiungi il file CSV al FormData
      formData.append("REPORT_FileExport", csvFile);

      const response = await axios.post(
        `${baseurl}/report/POST_Report.php`,
        formData,
        {
          headers: { "Content-Type": "multipart/form-data" },
        }
      );

      console.log(response.data.code);

      if (response.data.code === 200) {
        handleClose();
      } else {
        console.log(response.data);
        setError(response.data.message);
        // console.log(response.data);
      }
    } catch (err) {
      console.error("Errore durante la creazione del report:", err);
      setError("Errore di connessione con il server.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="modal-container">
      <button className="modal-btn-primary" onClick={handleShow}>
        Aggiungi Report
      </button>

      <Modal
        show={show}
        onClose={handleClose}
        title={`Crea Report per ${categoriaNome}`}
        error={error}
      >
        <form onSubmit={handleSubmit}>
          <div className="modal-form-group">
            <label>Nome</label>
            <input
              type="text"
              value={nome}
              onChange={(e) => setNome(e.target.value)}
              required
              autoComplete="off"
            />
          </div>

          <div className="modal-form-group">
            <label>Descrizione</label>
            <textarea
              value={descrizione}
              onChange={(e) => setDescrizione(e.target.value)}
              required
              autoComplete="off"
            ></textarea>
          </div>

          <div className="modal-actions">
            <button type="button" onClick={generateCSV}>
              Genera CSV
            </button>
            <button type="submit" disabled={loading}>
              {loading ? "Caricamento..." : "Salva"}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  );
}

export default AddReport;
