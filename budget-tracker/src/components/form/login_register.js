import React, { useState, useEffect } from "react";
import { FaEye, FaEyeSlash, FaSpinner } from "react-icons/fa";
import axios from "axios";

const baseurl = process.env.REACT_APP_BASE_URL;

function AuthForm() {
  console.log("login-reg");

  const [isLogin, setIsLogin] = useState(true);
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const [DATA, setFormData] = useState(() => {
    // Carica i dati da localStorage se disponibili, altrimenti stato iniziale
    const storedData = localStorage.getItem("UTENTE_Data");
    return storedData
      ? JSON.parse(storedData)
      : {
          UTENTE_Username: "",
          UTENTE_Mail: "",
          UTENTE_ID: "",
        };
  });

  useEffect(() => {
    // Salva i dati nel localStorage quando lo stato DATA cambia
    localStorage.setItem(
      "UTENTE_Data",
      JSON.stringify({
        UTENTE_Username: DATA.UTENTE_Username,
        UTENTE_Mail: DATA.UTENTE_Mail,
        UTENTE_ID: DATA.UTENTE_ID,
      })
    );
  }, [DATA]);

  const [errors, setErrors] = useState({});
  const commonDomains = [
    "@gmail.com",
    "@yahoo.com",
    "@outlook.com",
    "@hotmail.com",
  ];
  const [showDomainSuggestions, setShowDomainSuggestions] = useState(false);

  const validateEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  const validatePassword = (password) => password.length >= 3;

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...DATA, [name]: value });

    if (name === "UTENTE_Mail" && !value.includes("@")) {
      setShowDomainSuggestions(true);
    } else {
      setShowDomainSuggestions(false);
    }

    const newErrors = { ...errors };
    if (name === "UTENTE_Mail" && !validateEmail(value)) {
      newErrors.UTENTE_Mail = "L'email è invalida";
    } else if (name === "UTENTE_Password" && !validatePassword(value)) {
      newErrors.UTENTE_Password =
        "La password deve essere almeno di 3 caratteri";
    } else {
      delete newErrors[name];
    }
    setErrors(newErrors);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const newErrors = {};
    if (!validateEmail(DATA.UTENTE_Mail))
      newErrors.UTENTE_Mail = "Email non valida";
    if (!validatePassword(DATA.UTENTE_Password))
      newErrors.UTENTE_Password =
        "La password deve essere almeno di 3 caratteri";
    if (!isLogin && !DATA.UTENTE_Username.trim())
      newErrors.UTENTE_Username = "L'username è obbligatorio";

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      setLoading(false);
      return;
    }

    try {
      if (isLogin) {
        const formData = new FormData();
        formData.append("UTENTE_Mail", DATA.UTENTE_Mail);
        formData.append("UTENTE_Password", DATA.UTENTE_Password);

        axios
          .post(baseurl + "/utente/login.php", formData, {
            headers: { "Content-Type": "multipart/form-data" },
          })
          .then((res) => {
            localStorage.setItem("login", true);
            const userData = {
              UTENTE_Username: res.data.UTENTE_Username,
              UTENTE_Mail: res.data.UTENTE_Mail,
              UTENTE_ID: res.data.UTENTE_ID,
            };
            localStorage.setItem("UTENTE_Data", JSON.stringify(userData));
            window.location.href = "/Home";
          })
          .catch((error) => {
            handleError(error);
          });
      } else {
        const formData = new FormData();
        formData.append("UTENTE_Username", DATA.UTENTE_Username);
        formData.append("UTENTE_Mail", DATA.UTENTE_Mail);
        formData.append("UTENTE_Password", DATA.UTENTE_Password);

        axios
          .post(baseurl + "/utente/register.php", formData, {
            headers: { "Content-Type": "multipart/form-data" },
          })
          .then(() => {
            localStorage.setItem("login", false);
          })
          .catch((error) => {
            handleError(error);
          });
      }
    } finally {
      setLoading(false);
    }
  };

  const handleError = (error) => {
    if (error.status) {
      switch (error.status) {
        case 401:
          setErrors({ submit: "Credenziali errate" });
          break;
        case 400:
          setErrors({ submit: "Credenziali non valide" });
          break;
        case 404:
          setErrors({ submit: "Email specificata non esiste" });
          break;
        default:
          setErrors({
            submit: "Qualcosa è andato storto, riprova più tardi",
          });
      }
    }
  };

  const handleDomainSelect = (domain) => {
    setFormData({
      ...DATA,
      UTENTE_Mail: DATA.UTENTE_Mail.split("@")[0] + domain,
    });
    setShowDomainSuggestions(false);
  };

  return (
    <div className="auth-container">
      <div className="auth-box">
        <h2 className="auth-title">{isLogin ? "Accedi" : "Registrati"}</h2>
        <div className="auth-toggle">
          <button
            className={`toggle-btn ${isLogin ? "active" : ""}`}
            onClick={() => setIsLogin(true)}
          >
            Login
          </button>
          <button
            className={`toggle-btn ${!isLogin ? "active" : ""}`}
            onClick={() => setIsLogin(false)}
          >
            Register
          </button>
        </div>
        <form onSubmit={handleSubmit} className="auth-form">
          {!isLogin && (
            <div className="form-group">
              <label>Username</label>
              <input
                name="UTENTE_Username"
                type="text"
                value={DATA.UTENTE_Username}
                onChange={handleInputChange}
                placeholder="Username"
                className="form-input"
              />
              {errors.UTENTE_Username && (
                <p className="error-message">{errors.UTENTE_Username}</p>
              )}
            </div>
          )}
          <div className="form-group">
            <label>Email address</label>
            <input
              name="UTENTE_Mail"
              type="email"
              value={DATA.UTENTE_Mail}
              onChange={handleInputChange}
              placeholder="Email address"
              className="form-input"
            />
            {errors.UTENTE_Mail && (
              <p className="error-message">{errors.UTENTE_Mail}</p>
            )}
            {showDomainSuggestions && (
              <div className="domain-suggestions">
                {commonDomains.map((domain) => (
                  <button
                    key={domain}
                    onClick={() => handleDomainSelect(domain)}
                    className="domain-btn"
                  >
                    {DATA.UTENTE_Mail.split("@")[0] + domain}
                  </button>
                ))}
              </div>
            )}
          </div>
          <div className="form-group">
            <label>Password</label>
            <div className="password-input-container">
              <input
                name="UTENTE_Password"
                type={showPassword ? "text" : "password"}
                value={DATA.UTENTE_Password}
                onChange={handleInputChange}
                placeholder="Password"
                className="form-input"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="password-toggle-btn"
              >
                {showPassword ? <FaEyeSlash /> : <FaEye />}
              </button>
            </div>
            {errors.UTENTE_Password && (
              <p className="error-message">{errors.UTENTE_Password}</p>
            )}
          </div>
          <button type="submit" disabled={loading} className="submit-btn">
            {loading ? (
              <FaSpinner className="spinner" />
            ) : isLogin ? (
              "Sign in"
            ) : (
              "Register"
            )}
          </button>
          {errors.submit && <p className="error-message">{errors.submit}</p>}
        </form>
      </div>
    </div>
  );
}

export default AuthForm;
