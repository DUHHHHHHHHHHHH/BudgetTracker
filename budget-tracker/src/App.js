import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "./App.css";

// IMPORTS
import _404 from "./pages/_404/_404"; // pagina nn esistente.
import LOGIN from "./pages/Login/Login"; // LOGIN e REGISTER per l'utente.
import ADMINLOGIN from "./pages/Admin/AdminLogin"; // LOGIN e REGISTER per l'admin.
import HOME from "./pages/Home/Home"; // home, pagina dove l'utente una volta loggatato accede alle funzionalità.
import ADMIN from "./pages/Admin/Admin"; // pagina admin, dove l'utente admin può aggiungere, modificare e eliminare le tipologie.
import PROFILE from "./pages/Profile/Profile"; // pagina del profilo, dove l'utente può modificare il proprio profilo
import CATEGORIA from "./pages/Categoria/Categoria"; // pagina creazione, editing e cancellazione delle categorie.
import GESTIONALE from "./pages/Categorie/Categorie"; // pagina dove l'utente gestisce transizioni e milestones.
import REPORT from "./pages/Report/Report"; // pagina dei report, dove l'utente può vedere i grafici.

function App() {
  return (
    <Router>
      <Routes>
        <Route index element={<LOGIN />} />
        <Route path="*" element={<_404 />} />
        <Route path="/Home" element={<HOME />} />
        <Route path="/Admin" element={<ADMIN />} />
        <Route path="/Profilo" element={<PROFILE />} />
        <Route path="/Categoria" element={<CATEGORIA />} />
        <Route path="/Gestionale" element={<GESTIONALE />} />
        <Route path="/Report" element={<REPORT />} />
        <Route path="/AdminLogin" element={<ADMINLOGIN />} />
      </Routes>
    </Router>
  );
}

export default App;
