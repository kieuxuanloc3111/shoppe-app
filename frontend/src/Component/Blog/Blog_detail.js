import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

import Rate from "./Rate";
import ListComment2 from "./List_comment2";
import Comment from "./Comment";

const Blog_detail = () => {
  const params = useParams();
  const [data, setData] = useState(null);

  const [comments, setComments] = useState([]);

  const [replyTarget, setReplyTarget] = useState(null);

  useEffect(() => {
    const fetchDetail = async () => {
      try {
        const res = await axios.get(
          `http://localhost/laravel8/laravel8/public/api/blog/detail/${params.id}`
        );

        setData(res.data.data);

        const apiComments = res.data.data.comment;
        setComments(Array.isArray(apiComments) ? apiComments : []);
      } catch (err) {
        console.log(err);
        setComments([]);
      }
    };

    fetchDetail();
  }, [params.id]);

  //  Nhận comment mới từ Comment.js
  const handleAddComment = (newCmt) => {
    setComments((prev) => [...prev, newCmt]);
  };

  const handleReplyClick = (comment) => {
    setReplyTarget(comment);
  };

  const cancelReply = () => {
    setReplyTarget(null);
  };

  if (!data) return <p>Đang tải...</p>;

  return (
    <section>
      <div className="container">
        <div className="row">
          <div className="col-sm-9">
            <div className="blog-post-area">
              <h2 className="title text-center">Latest From our Blog</h2>

              <div className="single-blog-post">
                <h3>{data.title}</h3>

                <div className="post-meta">
                  <ul>
                    <li>
                      <i className="fa fa-user" /> Author ID: {data.id_auth}
                    </li>
                    <li>
                      <i className="fa fa-calendar" /> {data.created_at}
                    </li>
                  </ul>
                </div>

                <img
                  src={`http://localhost/laravel8/laravel8/public/upload/Blog/image/${data.image}`}
                  alt=""
                />

                <p>{data.description}</p>

                <div
                  dangerouslySetInnerHTML={{ __html: data.content }}
                ></div>
              </div>
            </div>

            {/* rate  */}
            <Rate idBlog={params.id} />

            {/* list */}
            <ListComment2
              comments={comments}
              onReply={handleReplyClick}
            />

            {/* comment*/}
            <Comment
              idBlog={params.id}
              parentId={replyTarget ? replyTarget.id : 0}
              onAddComment={handleAddComment}
              onCancelReply={cancelReply}
            />
          </div>
        </div>
      </div>
    </section>
  );
};

export default Blog_detail;
