import React from "react";

const _404 = () => {
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
        <a
          href="/"
          style={{
            marginTop: "2rem",
            padding: "0.75rem 1.5rem",
            backgroundColor: "#2563eb",
            color: "white",
            borderRadius: "0.5rem",
            textDecoration: "none",
          }}
          onMouseOver={(e) => (e.target.style.backgroundColor = "#1d4ed8")}
          onMouseOut={(e) => (e.target.style.backgroundColor = "#2563eb")}
        >
          Go back home
        </a>
      </div>
    </div>
  );
};

export default _404;
