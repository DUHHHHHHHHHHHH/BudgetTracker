import React from "react";
import { useNavigate } from "react-router-dom";
import { useEffect } from "react";
import Sidebar from "../../components/sidebar/sidebar";
import axios from "axios";

function Home() {
  console.log("home");

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

    console.log(userData.UTENTE_Mail);

    const email = userData.UTENTE_Mail;
    const baseurl = process.env.REACT_APP_BASE_URL;

    console.log("Dati inviati:", email);

    const formData = new FormData();
    formData.append("UTENTE_Mail", email);

    axios
      .post(baseurl + "/utente/GET_Utente.php", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      })
      .then((res) => {
        console.log(res.data);
        const username = res.data.UTENTE_Username;
        localStorage.setItem("UTENTE_Username", username);
        const email = res.data.UTENTE_Mail;
        localStorage.setItem("UTENTE_Mail", email);
        const UID = res.data.UTENTE_ID;
        localStorage.setItem("UTENTE_ID", UID);

        setUsername(username);
        setUtenteId(UID);
        setEmail(email);
      })
      .catch((error) => {
        console.log(error);
      });
  }, [navigate]);

  return (
    <div>
      <Sidebar username={username} UID={utenteId} />
    </div>
  );
}

export default Home;
