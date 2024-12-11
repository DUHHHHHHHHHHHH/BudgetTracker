import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import Sidebar from "../../components/sidebar/sidebar";
import AddReport from "./AddReport/AddReport";
import { FaTrash, FaEdit, FaDownload } from "react-icons/fa";

function Report() {
  const navigate = useNavigate();
  const [username, setUsername] = useState("");
  const [utenteId, setUtenteId] = useState(0);
  const [reports, setReports] = useState([]);
  const [loading, setLoading] = useState(true);

  // Stati per la gestione delle categorie
  const [selectedCategoriaNome, setSelectedCategoriaNome] = useState("");
  const [selectedCategoriaId, setSelectedCategoriaId] = useState(0);
  const [selectedCategoriaBilancio, setSelectedCategoriaBilancio] = useState(0);
  const [isSelected, setIsSelected] = useState(false);

  // Verifica dell'utente e recupero dei dati
  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      navigate("/login");
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, [navigate]);

  // Fetch dei report
  useEffect(() => {
    const fetchReports = async () => {
      try {
        const baseurl = process.env.REACT_APP_BASE_URL;
        const formData = new FormData();
        formData.append("UTENTE_ID", utenteId);

        const response = await axios.post(
          `${baseurl}/report/GET_Reports.php`,
          formData,
          {
            headers: { "Content-Type": "multipart/form-data" },
          }
        );

        console.log(response.data);

        setReports(response.data);
        setLoading(false);
      } catch (error) {
        console.error("Errore durante il fetch dei report:", error);
        setReports([]);
        setLoading(false);
      }
    };

    if (utenteId) {
      fetchReports();
    }
  }, [utenteId]);

  // Gestione della selezione di una categoria
  const handleCategoriaSelect = (
    userId,
    categoriaNome,
    selected,
    categoriaid,
    budget
  ) => {
    setUtenteId(userId);
    setSelectedCategoriaNome(categoriaNome);
    setSelectedCategoriaId(categoriaid);
    setIsSelected(selected);
    setSelectedCategoriaBilancio(budget);
    setIsSelected(true);
  };

  // Aggiunta di un nuovo report
  const handleAddReport = (newReport) => {
    setReports([...reports, newReport]);
  };

  // Eliminazione di un report
  const handleDeleteReport = async (reportId) => {
    try {
      const baseurl = process.env.REACT_APP_BASE_URL;
      const formData = new FormData();
      formData.append("REPORT_ID", reportId);
      formData.append("UTENTE_ID", utenteId);

      await axios.post(`${baseurl}/report/DELETE_Report.php`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      setReports(reports.filter((report) => report.REPORT_ID !== reportId));
    } catch (error) {
      console.error("Errore durante l'eliminazione del report:", error);
    }
  };

  // Funzione per scaricare il file
  const handleOpenFile = (filePath) => {
    const baseurl = process.env.REACT_APP_BASE_URL;

    // Costruisci il percorso completo del file
    const fileUrl = `${baseurl}/../${filePath}`; // torno indietro perchè sarebbe l'url per le api LOL

    // Crea un link temporaneo per avviare il download
    const link = document.createElement("a");
    link.href = fileUrl;

    // Il nome del file per il download (opzionale, può essere lasciato come il nome originale del file)
    link.download = filePath.split("/").pop(); // Ottieni il nome del file dalla path

    // Avvia il download
    link.click();
  };

  if (loading) {
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
        Loading...
      </div>
    );
  }

  return (
    <div>
      <div style={{ display: "flex", width: "100%" }}>
        <Sidebar
          username={username}
          UID={utenteId}
          Pagina="Report"
          onCategoriaSelect={handleCategoriaSelect} // Passa la funzione di selezione categoria
        />

        <div className="container" style={{ backgroundColor: "#566a4f" }}>
          <h2 style={{ width: "100%", textAlign: "center" }}>I tuoi report</h2>
          <div
            style={{
              width: "auto",
              overflow: "auto",
              maxHeight: "250px",
              textAlign: "center",
              padding: "10px",
              display: "block ruby",
            }}
          >
            {reports.length === 0 ? (
              <div
                style={{
                  textAlign: "center",
                  fontSize: "1.5rem",
                  marginTop: "20px",
                }}
              >
                Nessun report trovato
              </div>
            ) : (
              <table className="table table-striped" style={{ width: "auto" }}>
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Descrizione</th>
                    <th>Data Generazione</th>
                    <th>Azioni</th>
                  </tr>
                </thead>
                <tbody>
                  {reports.map((report) => (
                    <tr key={report.REPORT_ID}>
                      <td>{report.REPORT_Nome}</td>
                      <td>{report.REPORT_Descrizione} </td>
                      <td>{report.REPORT_DataGenerazione.split(" ")[0]}</td>
                      <td>
                        <button
                          onClick={() => handleDeleteReport(report.REPORT_ID)}
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
                        <button
                          onClick={() =>
                            handleOpenFile(report.REPORT_FileExport)
                          }
                          style={{
                            backgroundColor: "#4c7daf",
                            color: "white",
                            padding: "8px 12px",
                            border: "none",
                            borderRadius: "4px",
                            cursor: "pointer",
                            transition: "background-color 0.3s ease",
                          }}
                        >
                          <FaDownload />
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </div>

          <div style={{ marginTop: "20px", textAlign: "center" }}>
            <h2>Aggiungi un nuovo report</h2>
            {/* Mostra il componente AddReport solo se una categoria è selezionata e non è 'default-dev' */}
            {isSelected && selectedCategoriaNome !== "default-dev" ? (
              <>
                <AddReport
                  categoriaNome={selectedCategoriaNome}
                  categoriaid={selectedCategoriaId}
                  utenteId={utenteId}
                  onAddReport={handleAddReport}
                />
              </>
            ) : (
              <p>Selezionare una categoria per poter generare il report.</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

export default Report;
