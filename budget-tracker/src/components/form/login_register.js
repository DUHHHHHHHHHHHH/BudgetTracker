import React, { useState } from "react";
import { FaEye, FaEyeSlash, FaSpinner } from "react-icons/fa";
import axios from "axios";

const baseurl = process.env.REACT_APP_BASE_URL;

const AuthForm = () => {
  const [isLogin, setIsLogin] = useState(true);
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const [formData, setFormData] = useState({
    UTENTE_Username: "",
    UTENTE_Mail: "",
    UTENTE_Password: "",
  });

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
    setFormData({ ...formData, [name]: value });

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
    if (!validateEmail(formData.UTENTE_Mail))
      newErrors.UTENTE_Mail = "Email non valida";
    if (!validatePassword(formData.UTENTE_Password))
      newErrors.UTENTE_Password =
        "La password deve essere almeno di 3 caratteri";
    if (!isLogin && !formData.UTENTE_Username.trim())
      newErrors.UTENTE_Username = "L'username è obbligatorio";

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      setLoading(false);
      return;
    }

    try {
      if (isLogin) {
        const formData = new FormData();
        console.log(formData);
        formData.append("UTENTE_Mail", formData.UTENTE_Mail);
        formData.append("UTENTE_Password", formData.UTENTE_Password);

        axios
          .post(baseurl + "/utente/login.php", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            localStorage.setItem("login", true);
            localStorage.setItem("UTENTE_Mail", res.data.mail);
            localStorage.setItem("UTENTE_ID", res.data.id);
            window.location.href = "/Home";
          })
          .catch((error) => {
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
                  break;
              }
            }
          });
      } else {
        const formData = new FormData();
        formData.append("UTENTE_Username", formData.UTENTE_Username);
        formData.append("UTENTE_Mail", formData.UTENTE_Mail);
        formData.append("UTENTE_Password", formData.UTENTE_Password);

        axios
          .post(baseurl + "/utente/register.php", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            console.log(response.data);
            localStorage.setItem("login", true);
            localStorage.setItem("UTENTE_Mail", formData.UTENTE_Mail);
            window.location.href = "/";
          })
          .catch((error) => {
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
                  break;
              }
            }
          });
      }
    } finally {
      setLoading(false);
    }
  };

  const handleDomainSelect = (domain) => {
    setFormData({
      ...formData,
      UTENTE_Mail: formData.UTENTE_Mail.split("@")[0] + domain,
    });
    setShowDomainSuggestions(false);
  };

  return (
    <div
      style={{
        minHeight: "100vh",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
      }}
    >
      <div style={{ width: "400px", textAlign: "center" }}>
        <div>
          <h2 style={{}}>{isLogin ? "Accedi" : "Registrati"}</h2>
        </div>

        <div>
          <div className="flex">
            <button onClick={() => setIsLogin(true)}>Login</button>
            <button onClick={() => setIsLogin(false)}>Register</button>
          </div>

          <form
            onSubmit={handleSubmit}
            className="flex"
            style={{ flexDirection: "column", width: "100%" }}
          >
            {!isLogin && (
              <div style={{ width: "100%" }}>
                <div
                  style={{
                    display: "flex",
                    flexDirection: "column",
                    width: "100%",
                  }}
                >
                  <label
                    htmlFor="UTENTE_Username"
                    style={{ width: "100%", textAlign: "left" }}
                  >
                    Username
                  </label>
                  <input
                    id="UTENTE_Username"
                    name="UTENTE_Username"
                    type="text"
                    value={formData.UTENTE_Username}
                    onChange={handleInputChange}
                    placeholder="Username"
                    style={{ width: "100%" }}
                  />
                  {errors.UTENTE_Username && <p>{errors.UTENTE_Username}</p>}
                </div>
              </div>
            )}

            <div style={{ width: "100%" }}>
              <div
                style={{
                  display: "flex",
                  flexDirection: "column",
                  width: "100%",
                }}
              >
                <label
                  htmlFor="UTENTE_Mail"
                  style={{ width: "100%", textAlign: "left" }}
                >
                  Email address
                </label>
                <input
                  id="UTENTE_Mail"
                  name="UTENTE_Mail"
                  type="email"
                  value={formData.UTENTE_Mail}
                  onChange={handleInputChange}
                  placeholder="Email address"
                  style={{ width: "100%" }}
                />
                {errors.UTENTE_Mail && <p>{errors.UTENTE_Mail}</p>}
                {showDomainSuggestions && (
                  <div style={{ width: "100%" }}>
                    {commonDomains.map((domain) => (
                      <button
                        key={domain}
                        type="button"
                        onClick={() => handleDomainSelect(domain)}
                        style={{ width: "100%" }}
                      >
                        {formData.UTENTE_Mail.split("@")[0] + domain}
                      </button>
                    ))}
                  </div>
                )}
              </div>
            </div>

            <div style={{ width: "100%" }}>
              <div
                style={{
                  display: "flex",
                  flexDirection: "column",
                  width: "100%",
                }}
              >
                <label
                  htmlFor="UTENTE_Password"
                  style={{ width: "100%", textAlign: "left" }}
                >
                  Password
                </label>
                <div style={{ display: "flex", width: "100%" }}>
                  <input
                    id="UTENTE_Password"
                    name="UTENTE_Password"
                    type={showPassword ? "text" : "password"}
                    value={formData.UTENTE_Password}
                    onChange={handleInputChange}
                    placeholder="Password"
                    style={{ width: "100%" }}
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                  >
                    {showPassword ? <FaEyeSlash /> : <FaEye />}
                  </button>
                </div>
                {errors.UTENTE_Password && <p>{errors.UTENTE_Password}</p>}
              </div>
            </div>

            <div style={{ width: "100%" }}>
              <button
                type="submit"
                disabled={loading}
                style={{ width: "100%" }}
              >
                {loading ? (
                  <FaSpinner />
                ) : (
                  <span>{isLogin ? "Sign in" : "Register"}</span>
                )}
              </button>
            </div>
            {errors.submit && <p>{errors.submit}</p>}
          </form>
        </div>
      </div>
    </div>
  );
};

export default AuthForm;
