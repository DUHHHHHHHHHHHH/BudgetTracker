import React from "react";
import { useNavigate } from "react-router-dom";
import { useEffect } from "react";
import Sidebar from "../../components/sidebar/sidebar";
import axios from "axios";

function Report() {
  const navigate = useNavigate();
  const [username, setUsername] = React.useState("");
  const [utenteId, setUtenteId] = React.useState(0);
  const [email, setEmail] = React.useState("");

  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      navigate("/login");
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setEmail(userData.UTENTE_Mail);
    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, [navigate]);

  return (
    <div>
      <Sidebar username={username} UID={utenteId} Pagina={"Report"} />
    </div>
  );
}

export default Report;
