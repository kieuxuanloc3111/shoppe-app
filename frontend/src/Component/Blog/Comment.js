import React, { useState } from "react";
import axios from "axios";

const Comment = ({ idBlog, parentId = 0, onAddComment, onCancelReply }) => {
  const [comment, setComment] = useState("");
  const [error, setError] = useState("");

  const token = localStorage.getItem("token");
  const auth = localStorage.getItem("auth");
  const user = auth ? JSON.parse(auth) : null;

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!token || !user) {
      alert("Vui lòng đăng nhập trước khi bình luận!");
      return;
    }

    if (!comment.trim()) {
      setError("Bạn chưa nhập bình luận!");
      return;
    }

    setError("");

    const url =
      "http://localhost/laravel8/laravel8/public/api/blog/comment/" + idBlog;

    const formData = new FormData();
    formData.append("id_blog", idBlog);
    formData.append("id_user", user.id);
    formData.append("comment", comment);
    formData.append("name_user", user.name);
    formData.append("image_user", user.avatar);
    formData.append("id_comment", parentId);

    try {
      const res = await axios.post(url, formData, {
        headers: {
          Authorization: "Bearer " + token,
          Accept: "application/json",
          "Content-Type": "multipart/form-data",
        },
      });

      if (res.data && res.data.data) {
        onAddComment(res.data.data); //  trả comment mới lên Detail
        console.log(res.data.data)
        setComment("");

        if (onCancelReply) {onCancelReply();} // thoát rep
      }
    } catch (err) {
      console.log(err);
      alert("Lỗi khi gửi bình luận!");
    }
  };

  return (
    <div className="replay-box" style={{ marginTop: 20 }}>
      {parentId !== 0 && (
        <p style={{ color: "blue" }}>
          Đang trả lời bình luận 
          <span
            style={{ color: "red", cursor: "pointer" }}
            onClick={onCancelReply}
          >
             Hủy
          </span>
        </p>
      )}

      <form onSubmit={handleSubmit}>
        <textarea
          rows="4"
          value={comment}
          onChange={(e) => setComment(e.target.value)}
          placeholder={
            parentId === 0 ? "Nhập bình luận..." : "Nhập trả lời bình luận..."
          }
        ></textarea>

        {error && <p style={{ color: "red" }}>{error}</p>}

        <button className="btn btn-primary" type="submit">
          {parentId === 0 ? "Gửi bình luận" : "Gửi trả lời"}
        </button>
      </form>
    </div>
  );
};

export default Comment;
