import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "./App.css";

// IMPORTS
import _404 from "./pages/_404/_404"; // pagina nn esistente.
import LOGIN from "./pages/Login/Login"; // LOGIN e REGISTER.
import HOME from "./pages/Home/Home"; // home, pagina dove l'utente una volta loggatato accede alle funzionalità.
import ADMIN from "./pages/Admin/Admin"; // pagina admin, dove l'utente admin può aggiungere le categorie.
import PROFILE from "./pages/Profile/Profile"; // pagina del profilo, dove l'utente può modificare il proprio profilo
import CATEGORIE from "./pages/Categorie/Categorie"; // pagina delle categorie, dove l'utente può aggiungere le categorie, vedere i grafici.
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
        <Route path="/Categorie" element={<CATEGORIE />} />
        <Route path="/Report" element={<REPORT />} />
      </Routes>
    </Router>
  );
}

export default App;
