import React, { useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

const Login = () => {
  const navigate = useNavigate();

  const [form, setForm] = useState({
    email: "",
    password: "",
    level: 0,
  });

  const [errors, setErrors] = useState({});

  const handleInput = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    let newErrors = {};
    if (!form.email) newErrors.email = "Email chưa nhập";
    if (!form.password) newErrors.password = "Password chưa nhập";

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    setErrors({});

    try {
      const res = await axios.post(
        "http://localhost/laravel8/laravel8/public/api/login",
        form
      );

      console.log("API login trả về:", res.data);

      if (res.data.token) {
        localStorage.setItem("token", res.data.token);
        localStorage.setItem("auth", JSON.stringify(res.data.Auth));

        window.dispatchEvent(new Event("login"));

        alert("Đăng nhập thành công!");
        navigate("/");
      } else {
        alert("Đăng nhập thất bại!");
      }
    } catch (error) {
      alert("Lỗi khi gửi API!");
      console.error(error);
    }
  };

  return (
    <section id="form">
      <div className="container">
        <div className="row">

          <div className="col-sm-4 col-sm-offset-4">
            <div className="login-form">

              <h2>Login to your account</h2>

              <form onSubmit={handleSubmit}>
                <input
                  type="email"
                  name="email"
                  placeholder="Email Address"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.email}</p>

                <input
                  type="password"
                  name="password"
                  placeholder="Password"
                  onChange={handleInput}
                />
                <p style={{ color: "red" }}>{errors.password}</p>

                <span>
                  <input type="checkbox" /> Keep me signed in
                </span>

                <button type="submit" className="btn btn-default" style={{ marginTop: "10px" }}>
                  Login
                </button>
              </form>

            </div>
          </div>

        </div>
      </div>
    </section>
  );
};

export default Login;
