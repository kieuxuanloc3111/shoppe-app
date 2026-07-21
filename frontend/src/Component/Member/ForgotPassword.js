import React, { useState } from "react";
import axios from "axios";

const ForgotPassword = () => {
  const [email, setEmail] = useState("");
  const [msg, setMsg] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMsg("");
    try {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/forgot-password",
        { email }
      );
      setMsg(res.data.message);
    } catch (err) {
      setMsg(err.response?.data?.message || "Error");
    }
  };

  return (
    <section id="form">
      <div className="container">
        <div className="row">
          <div className="col-sm-4 col-sm-offset-4">
            <div className="login-form">
              <h2>Forgot password</h2>
              <form onSubmit={handleSubmit}>
                <input
                  type="email"
                  placeholder="Email Address"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                />
                <button type="submit" className="btn btn-default" style={{ marginTop: 10 }}>
                  Send reset link
                </button>
              </form>
              {msg && <p style={{ color: "green", marginTop: 10 }}>{msg}</p>}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ForgotPassword;
