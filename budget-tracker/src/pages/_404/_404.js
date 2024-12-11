import React, { useState, useEffect } from "react";

const _404 = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const loggedIn = localStorage.getItem("login") === "true";
    setIsLoggedIn(loggedIn);
  }, []);

  const handleRedirect = () => {
    if (isLoggedIn) {
      window.location.href = "/Home";
    } else {
      window.location.href = "/";
    }
  };

  return (
    <div>
      <div
        style={{
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          justifyContent: "center",
          minHeight: "100vh",
          backgroundColor: "#f3f4f6",
        }}
      >
        <h1
          style={{
            fontSize: "4rem",
            fontWeight: "bold",
            color: "#1f2937",
          }}
        >
          404
        </h1>
        <h2
          style={{
            fontSize: "1.5rem",
            fontWeight: "500",
            color: "#4b5563",
            marginTop: "1rem",
          }}
        >
          Page Not Found
        </h2>
        <p
          style={{
            color: "#6b7280",
            marginTop: "0.5rem",
          }}
        >
          La pagina che hai digitato non esiste.
        </p>
        <button
          onClick={handleRedirect}
          style={{
            marginTop: "2rem",
            padding: "0.75rem 1.5rem",
            backgroundColor: isLoggedIn ? "#2563eb" : "#9ca3af", // Disabilita il pulsante se non loggato
            color: "white",
            borderRadius: "0.5rem",
            border: "none",
            cursor: isLoggedIn ? "pointer" : "not-allowed",
            textDecoration: "none",
          }}
          onMouseOver={(e) => {
            if (isLoggedIn) e.target.style.backgroundColor = "#1d4ed8";
          }}
          onMouseOut={(e) => {
            if (isLoggedIn) e.target.style.backgroundColor = "#2563eb";
          }}
        >
          {isLoggedIn ? "Go back home" : "Login required"}
        </button>
      </div>
    </div>
  );
};

export default _404;
