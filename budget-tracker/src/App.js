import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "app.css";

// IMPORTS
import _404 from "./pages/_404/_404"; // pagina nn esistente.
import LOGIN from "./pages/Login/Login"; // LOGIN e REGISTER.
import HOME from "./pages/Home/Home"; // home, pagina dove l'utente una volta loggatato accede alle funzionalità.
import ADMIN from "./pages/Admin/Admin"; // pagina admin, dove l'utente admin può aggiungere le categorie.
import PROFILE from "./pages/Profile/Profile"; // pagina del profilo, dove l'utente può modificare il proprio profilo.
import ABOUT from "./pages/About/About"; // pagina about, dove l'utente può vedere informazioni sulla web app.

function App() {
  return (
    <Router>
      <Routes>
        <Route index element={<LOGIN />} />
        <Route path="*" element={<_404 />} />
        <Route path="/home" element={<HOME />} />
        <Route path="/admin" element={<ADMIN />} />
        <Route path="/profile" element={<PROFILE />} />
        <Route path="/about" element={<ABOUT />} />
      </Routes>
    </Router>
  );
}

export default App;
