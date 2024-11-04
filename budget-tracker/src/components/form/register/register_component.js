import React from "react";
import "../form.css";

function register_component() {
  return (
    <form>
      <div className="form-table-container">
        <div className="form-table-row">
          <div className="form-table-cell">
            <label>Username:</label>
          </div>
          <div className="form-table-cell">
            <input
              type="text"
              name="username"
              placeholder="username"
              id="username_register"
            />
          </div>
        </div>

        <div className="form-table-row">
          <div className="form-table-cell">
            <label>Email:</label>
          </div>
          <div className="form-table-cell">
            <input
              type="email"
              name="email"
              placeholder="email"
              id="email_register"
            />
          </div>
        </div>

        <div className="form-table-row">
          <div className="form-table-cell">
            <label>Password:</label>
          </div>
          <div className="form-table-cell">
            <input
              type="password"
              name="password"
              placeholder="password"
              id="password_register"
            />
          </div>
        </div>
        <div className="form-table-cell">
          <button type="submit">Register</button>
        </div>
      </div>
    </form>
  );
}

export default register_component;
