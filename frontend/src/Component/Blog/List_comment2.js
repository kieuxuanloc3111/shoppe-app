// List_comment2.js
import React from "react";

const ListComment2 = ({ comments = [], onReply }) => {
  if (!Array.isArray(comments)) comments = [];

  // Tách cha – con theo id_comment
  const parents = comments.filter(c => Number(c.id_comment) === 0);
  const children = comments.filter(c => Number(c.id_comment) !== 0);

  return (
    <div className="response-area">
      <h2>{comments.length} Comments</h2>

      <ul className="media-list">

        {parents.map((parent) => (
          <div key={parent.id}>

            {/* cmt cha*/}
            <li className="media">
              <a className="pull-left" href="#">
                <img
                  className="media-object"
                  src={`http://localhost/laravel8/laravel8/public/upload/user/avatar/${parent.image_user}`}
                  alt=""
                  style={{
                    width: 60,
                    height: 60,
                    objectFit: "cover",
                    borderRadius: 5
                  }}
                />
              </a>

              <div className="media-body">
                <ul className="sinlge-post-meta">
                  <li><i className="fa fa-user" /> {parent.name_user}</li>
                  <li><i className="fa fa-clock-o" /> {parent.created_at}</li>
                </ul>

                <p>{parent.comment}</p>

                <a
                  className="btn btn-primary"
                  onClick={() => onReply(parent)}
                  style={{ cursor: "pointer" }}
                >
                  <i className="fa fa-reply" /> Reply
                </a>
              </div>
            </li>

            {/* cmt con */}
            {children
              .filter(child => Number(child.id_comment) === parent.id)
              .map(child => (
                <li className="media second-media" key={child.id}>

                  <a className="pull-left" href="#">
                    <img
                      className="media-object"
                      src={`http://localhost/laravel8/laravel8/public/upload/user/avatar/${child.image_user}`}
                      alt=""
                      style={{
                        width: 60,
                        height: 60,
                        objectFit: "cover",
                        borderRadius: 5
                      }}
                    />
                  </a>

                  <div className="media-body">
                    <ul className="sinlge-post-meta">
                      <li><i className="fa fa-user" /> {child.name_user}</li>
                      <li><i className="fa fa-clock-o" /> {child.created_at}</li>
                    </ul>

                    <p>{child.comment}</p>

                    <a
                      className="btn btn-primary"
                      onClick={() => onReply(child)}
                      style={{ cursor: "pointer" }}
                    >
                      <i className="fa fa-reply" /> Reply
                    </a>
                  </div>
                </li>
              ))}
          </div>
        ))}

      </ul>
    </div>
  );
};

export default ListComment2;
