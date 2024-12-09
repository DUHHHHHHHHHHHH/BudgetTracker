import React from "react";
import { Line, Bar } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
);

function GraficiChartJS() {
  const datiLinea = {
    labels: [
      "Gennaio",
      "Febbraio",
      "Marzo",
      "Aprile",
      "Maggio",
      "Giugno",
      "Luglio",
      "Agosto",
      "Settembre",
      "Ottobre",
      "Novembre",
      "Dicembre",
    ],
    datasets: [
      {
        label: "Vendite 2023",
        data: [12, 19, 3, 5, 2, 3],
        borderColor: "rgb(75, 192, 192)",
        tension: 0.1,
      },
    ],
  };

  const datiBarre = {
    labels: ["Rosso", "Blu", "Giallo", "Verde", "Viola", "Arancione"],
    datasets: [
      {
        label: "Valore Transazioni",
        data: [12, 19, 3, 5, 2, 3],
        backgroundColor: [
          "rgba(255, 99, 132, 0.5)",
          "rgba(54, 162, 235, 0.5)",
          "rgba(255, 206, 86, 0.5)",
          "rgba(75, 192, 192, 0.5)",
          "rgba(153, 102, 255, 0.5)",
          "rgba(255, 159, 64, 0.5)",
        ],
      },
    ],
  };

  const options = {
    responsive: true,
    maintainAspectRatio: false, // Permette un controllo personalizzato delle dimensioni
  };

  return (
    <div
      style={{
        padding: "20px",
        display: "flex",
        flexDirection: "row",
        color: "white",
      }}
    >
      <div style={{ marginBottom: "20px", height: "150px", width: "50%" }}>
        <h2>Grafico Lineare</h2>
        <Line data={datiLinea} options={options} />
      </div>
      <div style={{ marginBottom: "20px", height: "150px", width: "50%" }}>
        <h2>Grafico a Barre</h2>
        <Bar data={datiBarre} options={options} />
      </div>
    </div>
  );
}

export default GraficiChartJS;
