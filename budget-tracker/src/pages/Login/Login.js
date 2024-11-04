import React, { useState } from "react";
import LoginComponent from "../../components/form/login/login_component";
import RegisterComponent from "../../components/form/register/register_component";
import Switch from "../../components/misc/switch/switch";

function Login() {
  const [isLoginMode, setIsLoginMode] = useState(true);

  const toggleMode = () => {
    setIsLoginMode(!isLoginMode);
  };

  return (
    <>
      <div>
        <Switch funct={toggleMode} />
        {isLoginMode ? (
          <>
            <LoginComponent />
          </>
        ) : (
          <>
            <RegisterComponent />
          </>
        )}
      </div>
    </>
  );
}
export default Login;
