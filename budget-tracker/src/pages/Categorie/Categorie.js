import React from "react";
import { useNavigate } from "react-router-dom";
import { useEffect } from "react";
import Sidebar from "../../components/sidebar/sidebar";

import ChartJSs from "./ChartJS/ChartJS";
import Transizioni from "./Transizioni/Transizioni";
import Milestones from "./Milestones/Milestones";

function Categorie() {
  const navigate = useNavigate();
  const [username, setUsername] = React.useState("");
  const [utenteId, setUtenteId] = React.useState(0);

  const [selectedCategoriaNome, setSelectedCategoriaNome] = React.useState("");
  const [selectedCategoriaId, setSelectedCategoriaId] = React.useState(0);
  const [selectedCategoriaBilancio, setSelectedCategoriaBilancio] =
    React.useState(0);

  const [isSelected, setIsSelected] = React.useState(true);

  const [allTransizioni, setAllTransizioni] = React.useState([]); // variabile che viene contenuta dalle transizioni da passare ai grafici
  const [resetKey, setResetKey] = React.useState(0); // Nuovo stato per forzare il reset dei grafici

  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      navigate("/login");
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, [navigate]);

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
    setResetKey((prevKey) => prevKey + 1); // Incrementa la chiave per forzare il reset
    setAllTransizioni([]); // Resetta le transizioni
  };

  const handleTransizioniUpdate = (transizioni) => {
    setAllTransizioni(transizioni);
  };

  const handleMilestonesUpdate = (milestones) => {
    // Gestisce l'aggiornamento delle milestones
    // nel mio caso non serve perchè non li passo a nessun altro componente.
    // creata la funzione perchè potrebbe essere usata in futuro.
  };

  return (
    <div>
      <div style={{ display: "flex", width: "100%" }}>
        <Sidebar
          username={username}
          UID={utenteId}
          Pagina="Categorie"
          onCategoriaSelect={handleCategoriaSelect}
        />
        <div className="container">
          <div className="chart-container">
            <ChartJSs
              key={`${selectedCategoriaId}-${resetKey}`}
              transizioni={allTransizioni}
              categoriaBudget={selectedCategoriaBilancio}
            />
          </div>
          <div className="data-container">
            <div className="transactions">
              <Transizioni
                boolSelected={isSelected}
                categoriaNome={selectedCategoriaNome}
                categoriaid={selectedCategoriaId}
                utenteId={utenteId}
                onTransizioniUpdate={handleTransizioniUpdate}
              />
            </div>
            <div className="milestones">
              <Milestones
                boolSelected={isSelected}
                categoriaNome={selectedCategoriaNome}
                categoriaid={selectedCategoriaId}
                utenteId={utenteId}
                onMilestonesUpdate={handleMilestonesUpdate}
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
export default Categorie;
