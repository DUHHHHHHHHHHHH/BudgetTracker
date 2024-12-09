import React, { useRef } from "react";
import { Line, Bar, Pie } from "react-chartjs-2";
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
  ArcElement,
} from "chart.js";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
);

function GraficiChartJS({ transizioni, categoriaBudget }) {
  const monthNames = [
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
  ];

  const yearlyMonthlyBalance = {};
  const monthlyTransactions = new Array(12).fill(0);
  const typeCount = {};

  // UseRef per mantenere il colorMap tra i render
  const colorMap = useRef({});

  // Funzione per generare un colore casuale
  const generateColor = () => {
    return `rgb(${Math.floor(Math.random() * 255)}, ${Math.floor(
      Math.random() * 255
    )}, ${Math.floor(Math.random() * 255)})`;
  };

  // Funzione per ottenere un colore unico per una chiave (es. anno)
  const getColorForKey = (key) => {
    if (!colorMap.current[key]) {
      colorMap.current[key] = generateColor();
    }
    return colorMap.current[key];
  };

  // Elaborazione delle transizioni
  transizioni.forEach((transaction) => {
    const date = new Date(transaction.TRANSIZIONE_Data);
    const month = date.getMonth();
    const year = date.getFullYear();

    // Inizializza l'array per l'anno se non esiste
    if (!yearlyMonthlyBalance[year]) {
      yearlyMonthlyBalance[year] = new Array(12).fill(0);
    }

    // Calcolo del bilancio mensile per anno
    if (transaction.TRANSIZIONE_Tipo === "ENTRATA") {
      yearlyMonthlyBalance[year][month] += parseFloat(
        transaction.TRANSIZIONE_QTA
      );
    } else {
      yearlyMonthlyBalance[year][month] -= parseFloat(
        transaction.TRANSIZIONE_QTA
      );
    }

    // Conteggio transazioni per mese
    monthlyTransactions[month] += 1;

    // Conteggio per tipo
    if (!typeCount[transaction.TRANSIZIONE_Tipo]) {
      typeCount[transaction.TRANSIZIONE_Tipo] = 0;
    }
    typeCount[transaction.TRANSIZIONE_Tipo] += 1;
  });

  const datiLinea = {
    labels: monthNames,
    datasets: Object.entries(yearlyMonthlyBalance).map(([year, data]) => ({
      label: `Bilancio ${year}`,
      data: data,
      borderColor: getColorForKey(year), // Usa un colore unico per l'anno
      tension: 0.1,
    })),
  };

  const datiBarre = {
    labels: monthNames,
    datasets: [
      {
        label: "Numero Transazioni per Mese",
        data: monthlyTransactions,
        backgroundColor: "rgba(54, 162, 235, 0.5)",
      },
    ],
  };

  const datiTorta = {
    labels: Object.keys(typeCount),
    datasets: [
      {
        data: Object.values(typeCount),
        backgroundColor: ["rgba(75, 192, 192, 0.5)", "rgba(255, 99, 132, 0.5)"],
      },
    ],
  };

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: false,
      },
    },
  };

  return (
    <div
      style={{
        padding: "4px",
        display: "flex",
        flexDirection: "row",
        flexWrap: "wrap",
        color: "white",
        justifyContent: "center",
        alignItems: "center",
        textAlign: "center",
      }}
    >
      <div style={{ marginBottom: "20px", height: "150px", width: "33%" }}>
        <h2>Bilancio della Categoria</h2> {categoriaBudget}
        <Line data={datiLinea} options={options} />
      </div>
      <div style={{ marginBottom: "20px", height: "150px", width: "33%" }}>
        <h2>Tipo + Utilizzato</h2>
        <Pie data={datiTorta} options={options} />
      </div>
      <div style={{ marginBottom: "20px", height: "150px", width: "34%" }}>
        <h2>QTA Transazioni a MESE</h2>
        <Bar data={datiBarre} options={options} />
      </div>
    </div>
  );
}

export default GraficiChartJS;
