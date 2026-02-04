import React from 'react';
import { Link } from 'react-router-dom';

const Index = () => {
  return (
    <div style={{ textAlign: "center", marginTop: "40px" }}>
      <h2>Welcome</h2>

      <div style={{ marginTop: "20px" }}>
        <Link 
          to="/login"
          style={{
            display: "inline-block",
            marginRight: "20px",
            padding: "10px 20px",
            background: "#3498db",
            color: "#fff",
            borderRadius: "6px",
            textDecoration: "none"
          }}
        >
          Login
        </Link>

        <Link 
          to="/register"
          style={{
            display: "inline-block",
            padding: "10px 20px",
            background: "#2ecc71",
            color: "#fff",
            borderRadius: "6px",
            textDecoration: "none"
          }}
        >
          Register
        </Link>
      </div>
    </div>
  );
};

export default Index;
