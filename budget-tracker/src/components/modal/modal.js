import React from "react";

function Modal({ show, onClose, title, children, error }) {
  if (!show) return null;

  return (
    <div style={{ color: "#333" }}>
      <div className="modal-overlay">
        <div className="modal-content">
          <div className="modal-header">
            <h2>{title}</h2>
            <button className="modal-close-button" onClick={onClose}>
              ×
            </button>
          </div>
          {error && (
            <div
              className="modal-error"
              style={{ color: "red", padding: "10px" }}
            >
              {error}
            </div>
          )}
          <div className="modal-body">{children}</div>
        </div>
      </div>
    </div>
  );
}

export default Modal;
