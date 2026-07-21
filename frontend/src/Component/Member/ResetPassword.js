import React, { useState } from "react";
import axios from "axios";
import { useSearchParams, useNavigate } from "react-router-dom";

const ResetPassword = () => {
  const [params] = useSearchParams();
  const navigate = useNavigate();

  const token = params.get("token") || "";
  const emailFromLink = params.get("email") || "";

  const [form, setForm] = useState({
    email: emailFromLink,
    password: "",
    password_confirmation: "",
  });
  const [msg, setMsg] = useState("");

  const onChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMsg("");
    try {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/reset-password",
        { ...form, token }
      );
      setMsg(res.data.message);
      if (res.data.response === "success") {
        setTimeout(() => navigate("/login"), 1200);
      }
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
              <h2>Reset password</h2>
              <form onSubmit={handleSubmit}>
                <input
                  type="email"
                  name="email"
                  placeholder="Email Address"
                  value={form.email}
                  onChange={onChange}
                />
                <input
                  type="password"
                  name="password"
                  placeholder="New password"
                  onChange={onChange}
                />
                <input
                  type="password"
                  name="password_confirmation"
                  placeholder="Confirm password"
                  onChange={onChange}
                />
                <button type="submit" className="btn btn-default" style={{ marginTop: 10 }}>
                  Reset
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

export default ResetPassword;
