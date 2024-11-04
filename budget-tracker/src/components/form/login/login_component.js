import React from "react";
import "../form.css";

function login_component() {
  return (
    <div>
      <form>
        <div className="form-table-container">
          <div className="form-table-row">
            <div className="form-table-cell">
              <label>Email:</label>
            </div>
            <div className="form-table-cell">
              <input type="email" name="email" placeholder="email" />
            </div>
          </div>

          <div className="form-table-row">
            <div className="form-table-cell">
              <label>Password:</label>
            </div>
            <div className="form-table-cell">
              <input type="password" name="password" placeholder="password" />
            </div>
          </div>

          <div className="form-table-row">
            <div className="form-table-cell">
              <button type="submit">Login</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
}

export default login_component;
