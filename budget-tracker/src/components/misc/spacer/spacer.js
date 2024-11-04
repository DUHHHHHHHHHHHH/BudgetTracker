import React from "react";

const Spacer = ({ width, height, measure = "px" }) => {
  const style = {
    width: `${width}${measure}`,
    height: `${height}${measure}`,
    display: "inline-block",
  };

  return <div style={style} />;
};

export default Spacer;
