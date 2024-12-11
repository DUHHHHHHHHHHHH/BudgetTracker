import React, { useState, useEffect } from "react";
import { FaEye, FaEyeSlash, FaSpinner, FaTimes } from "react-icons/fa"; // Aggiungi FaTimes per l'icona "X"
import axios from "axios";
import Sidebar from "../../components/sidebar/sidebar";

function Profile() {
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [utenteId, setUtenteId] = useState(0);
  const [isEditing, setIsEditing] = useState(false); // Stato per la modalità di modifica
  const [newUsername, setNewUsername] = useState("");
  const [newEmail, setNewEmail] = useState("");
  const [newPassword, setNewPassword] = useState(""); // Nuova password
  const [currentPassword, setCurrentPassword] = useState(""); // Password corrente per la verifica
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (localStorage.getItem("login") === null) {
      window.location.href = "/login"; // Redirige all'accesso se non loggato
    }

    const storedData = localStorage.getItem("UTENTE_Data");
    const userData = JSON.parse(storedData);

    setEmail(userData.UTENTE_Mail);
    setUsername(userData.UTENTE_Username);
    setUtenteId(userData.UTENTE_ID);
  }, []);

  const handleEditClick = () => {
    setIsEditing(true);
    setNewUsername(username);
    setNewEmail(email);
  };

  const handleCancelClick = () => {
    setIsEditing(false);
    setNewUsername(username);
    setNewEmail(email);
    setNewPassword(""); // Resetta la nuova password se annullata
    setCurrentPassword(""); // Resetta la password corrente se annullata
  };

  const handleSaveClick = async () => {
    setLoading(true);

    try {
      const baseurl = process.env.REACT_APP_BASE_URL;

      const formData = new FormData();
      formData.append("UTENTE_ID", utenteId);
      formData.append("UTENTE_NewUsername", newUsername);
      formData.append("UTENTE_NewMail", newEmail);
      formData.append("UTENTE_Password", currentPassword);

      const response = await axios.post(
        `${baseurl}/utente/PUT_Utente.php`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.code === 200) {
        setUsername(newUsername);
        setEmail(newEmail);
        setIsEditing(false);
      } else {
        console.log("Errore durante l'aggiornamento: ", response.data);
      }
    } catch (error) {
      console.log("Errore durante l'aggiornamento: ", error.message);
    }
    setLoading(false);
  };

  return (
    <div className="profile-container">
      <Sidebar username={username} UID={utenteId} Pagina={"Profilo"} />
      <div
        className="container"
        style={{ backgroundColor: "rgb(46, 94, 94)", alignItems: "center" }}
      >
        <div className="profile-box" style={{ width: "400px" }}>
          <h2>Profilo</h2>
          {isEditing && (
            <button
              onClick={handleCancelClick}
              className="cancel-btn"
              style={{
                position: "absolute",
                top: "10px",
                right: "10px",
                backgroundColor: "transparent",
                border: "none",
                fontSize: "24px",
                color: "#fff",
              }}
            >
              <FaTimes />
            </button>
          )}
          <div
            className="form-group"
            style={{ marginBottom: "10px", textAlign: "center" }}
          >
            <label>Username</label>
            {isEditing ? (
              <input
                type="text"
                value={newUsername}
                onChange={(e) => setNewUsername(e.target.value)}
                className="form-input"
              />
            ) : (
              <span>{username}</span>
            )}
          </div>
          <div
            className="form-group"
            style={{ marginBottom: "10px", textAlign: "center" }}
          >
            <label>Email</label>
            {isEditing ? (
              <input
                type="email"
                value={newEmail}
                onChange={(e) => setNewEmail(e.target.value)}
                className="form-input"
              />
            ) : (
              <span>{email}</span>
            )}
          </div>
          <div
            className="form-group"
            style={{ marginBottom: "10px", textAlign: "center" }}
          >
            <label>Password:</label>
            {isEditing ? (
              <input
                type="password"
                value={currentPassword}
                onChange={(e) => setCurrentPassword(e.target.value)}
                className="form-input"
                placeholder="Password Corrente"
              />
            ) : (
              <span>******</span>
            )}
          </div>
          <div
            className="form-group"
            style={{ marginBottom: "10px", textAlign: "center" }}
          >
            {isEditing ? <label>Nuova Password</label> : <label></label>}
            {isEditing ? (
              <div className="password-input-container">
                <input
                  type={showPassword ? "text" : "password"}
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  className="form-input"
                  placeholder="Nuova Password (Opzionale)"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="password-toggle-btn"
                >
                  {showPassword ? <FaEyeSlash /> : <FaEye />}
                </button>
              </div>
            ) : (
              <span></span>
            )}
          </div>
          <div
            className="form-group"
            style={{ marginBottom: "10px", textAlign: "center" }}
          >
            {isEditing ? (
              <button
                onClick={handleSaveClick}
                className="submit-btn"
                disabled={loading}
                style={{ backgroundColor: "rgb(53 53 53)" }}
              >
                {loading ? <FaSpinner className="spinner" /> : "Salva"}
              </button>
            ) : (
              <button
                onClick={handleEditClick}
                className="submit-btn"
                style={{ backgroundColor: "rgb(53 53 53)" }}
              >
                Modifica
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

export default Profile;
