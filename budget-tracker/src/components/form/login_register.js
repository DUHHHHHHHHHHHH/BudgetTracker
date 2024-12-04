import React, { useState } from "react";
import { FaEye, FaEyeSlash, FaSpinner } from "react-icons/fa";

const AuthForm = () => {
  const [isLogin, setIsLogin] = useState(true);
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    email: "",
    password: "",
    username: "",
    fullName: "",
  });
  const [errors, setErrors] = useState({});

  const commonDomains = [
    "@gmail.com",
    "@yahoo.com",
    "@outlook.com",
    "@hotmail.com",
  ];
  const [showDomainSuggestions, setShowDomainSuggestions] = useState(false);

  const validateEmail = (email) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };

  const validatePassword = (password) => {
    return password.length >= 8;
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });

    if (name === "email" && !value.includes("@")) {
      setShowDomainSuggestions(true);
    } else {
      setShowDomainSuggestions(false);
    }

    // Real-time validation
    const newErrors = { ...errors };
    if (name === "email" && !validateEmail(value)) {
      newErrors.email = "Invalid email format";
    } else if (name === "password" && !validatePassword(value)) {
      newErrors.password = "Password must be at least 8 characters long";
    } else {
      delete newErrors[name];
    }
    setErrors(newErrors);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    // Validate form
    const newErrors = {};
    if (!validateEmail(formData.email))
      newErrors.email = "Invalid email format";
    if (!validatePassword(formData.password))
      newErrors.password = "Password must be at least 8 characters long";
    if (!isLogin && !formData.username.trim())
      newErrors.username = "Username is required";
    if (!isLogin && !formData.fullName.trim())
      newErrors.fullName = "Full name is required";

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      setLoading(false);
      return;
    }

    // Simulate API call
    setTimeout(() => {
      setLoading(false);
      // Handle success
    }, 1500);
  };

  const handleDomainSelect = (domain) => {
    setFormData({ ...formData, email: formData.email.split("@")[0] + domain });
    setShowDomainSuggestions(false);
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg transition-all duration-300">
        <div className="text-center">
          <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
            {isLogin ? "Sign in to your account" : "Create your account"}
          </h2>
        </div>

        <div className="mt-8 space-y-6">
          <div className="flex justify-center space-x-4 mb-8">
            <button
              onClick={() => setIsLogin(true)}
              className={`px-4 py-2 rounded-md transition-all duration-300 ${
                isLogin ? "bg-blue-600 text-white" : "bg-gray-200 text-gray-700"
              }`}
              aria-label="Switch to login mode"
            >
              Login
            </button>
            <button
              onClick={() => setIsLogin(false)}
              className={`px-4 py-2 rounded-md transition-all duration-300 ${
                !isLogin
                  ? "bg-blue-600 text-white"
                  : "bg-gray-200 text-gray-700"
              }`}
              aria-label="Switch to register mode"
            >
              Register
            </button>
          </div>

          <form onSubmit={handleSubmit} className="mt-8 space-y-6">
            {!isLogin && (
              <div className="space-y-4">
                <div>
                  <label htmlFor="username" className="sr-only">
                    Username
                  </label>
                  <input
                    id="username"
                    name="username"
                    type="text"
                    autoComplete="username"
                    value={formData.username}
                    onChange={handleInputChange}
                    className={`appearance-none rounded-md relative block w-full px-3 py-2 border ${
                      errors.username ? "border-red-500" : "border-gray-300"
                    } placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all duration-300`}
                    placeholder="Username"
                    aria-label="Username input"
                  />
                  {errors.username && (
                    <p className="mt-2 text-sm text-red-600" role="alert">
                      {errors.username}
                    </p>
                  )}
                </div>

                <div>
                  <label htmlFor="fullName" className="sr-only">
                    Full Name
                  </label>
                  <input
                    id="fullName"
                    name="fullName"
                    type="text"
                    autoComplete="name"
                    value={formData.fullName}
                    onChange={handleInputChange}
                    className={`appearance-none rounded-md relative block w-full px-3 py-2 border ${
                      errors.fullName ? "border-red-500" : "border-gray-300"
                    } placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all duration-300`}
                    placeholder="Full Name"
                    aria-label="Full name input"
                  />
                  {errors.fullName && (
                    <p className="mt-2 text-sm text-red-600" role="alert">
                      {errors.fullName}
                    </p>
                  )}
                </div>
              </div>
            )}

            <div className="relative">
              <label htmlFor="email" className="sr-only">
                Email address
              </label>
              <input
                id="email"
                name="email"
                type="email"
                autoComplete="email"
                value={formData.email}
                onChange={handleInputChange}
                className={`appearance-none rounded-md relative block w-full px-3 py-2 border ${
                  errors.email ? "border-red-500" : "border-gray-300"
                } placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition-all duration-300`}
                placeholder="Email address"
                aria-label="Email input"
              />
              {errors.email && (
                <p className="mt-2 text-sm text-red-600" role="alert">
                  {errors.email}
                </p>
              )}
              {showDomainSuggestions && (
                <div className="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg">
                  {commonDomains.map((domain) => (
                    <button
                      key={domain}
                      type="button"
                      onClick={() => handleDomainSelect(domain)}
                      className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none"
                    >
                      {formData.email.split("@")[0] + domain}
                    </button>
                  ))}
                </div>
              )}
            </div>

            <div className="relative">
              <label htmlFor="password" className="sr-only">
                Password
              </label>
              <input
                id="password"
                name="password"
                type={showPassword ? "text" : "password"}
                autoComplete="current-password"
                value={formData.password}
                onChange={handleInputChange}
                className={`appearance-none rounded-md relative block w-full px-3 py-2 border ${
                  errors.password ? "border-red-500" : "border-gray-300"
                } placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm pr-10 transition-all duration-300`}
                placeholder="Password"
                aria-label="Password input"
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute inset-y-0 right-0 pr-3 flex items-center"
                aria-label={showPassword ? "Hide password" : "Show password"}
              >
                {showPassword ? (
                  <FaEyeSlash className="text-gray-400" />
                ) : (
                  <FaEye className="text-gray-400" />
                )}
              </button>
              {errors.password && (
                <p className="mt-2 text-sm text-red-600" role="alert">
                  {errors.password}
                </p>
              )}
            </div>

            <div>
              <button
                type="submit"
                disabled={loading}
                className="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 disabled:opacity-50"
                aria-label={
                  loading ? "Loading" : isLogin ? "Sign in" : "Register"
                }
              >
                {loading ? (
                  <FaSpinner className="animate-spin h-5 w-5" />
                ) : (
                  <span>{isLogin ? "Sign in" : "Register"}</span>
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default AuthForm;
