import React from "react";
import "./switch.css";

const switchh = ({ funct }) => {
  return (
    <div>
      <label className="switch">
        <input type="checkbox" onClick={funct} />
        <span className="slider round"></span>
      </label>
    </div>
  );
};
export default switchh;
