import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";

const Blog = () => {
  const [blogs, setBlogs] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const fetchBlogs = async (page = 1) => {
    try {
      const res = await axios.get(
        `http://127.0.0.1:8000/api/blog?page=${page}`
      );
      console.log(res.data);

      setBlogs(res.data.blog.data);
      setCurrentPage(res.data.blog.current_page);
      setLastPage(res.data.blog.last_page);

    } catch (error) {
      console.log("error:", error);
    }
  };

  useEffect(() => {
    fetchBlogs(currentPage);
  }, [currentPage]);

  return (
    <div>
      <section>
        <div className="container">
          <div className="row">
            <div className="col-sm-9">
              <div className="blog-post-area">

                <h2 className="title text-center">
                  Latest From our Blog
                </h2>

                {blogs.map((item) => (
                  <div className="single-blog-post" key={item.id}>

                    <h3>{item.title}</h3>

                    <div className="post-meta">
                      <ul>
                        <li>
                          <i className="fa fa-user" /> Admin
                        </li>

                        <li>
                          <i className="fa fa-calendar" />
                          {item.created_at}
                        </li>
                      </ul>
                    </div>

                    <img
                      src={`http://127.0.0.1:8000/${item.image}`}
                      alt={item.title}
                      style={{ width: "40%", height: "40%" }}
                    />

                    <div
                      dangerouslySetInnerHTML={{
                        __html: item.description
                      }}
                    />

                    <Link to={`/blog_detail/${item.id}`}>
                      Read more
                    </Link>

                  </div>
                ))}

                {/* pagination */}
                <div className="pagination-area">
                  <ul className="pagination">

                    {/* prev */}
                    <li>
                      <button
                        disabled={currentPage === 1}
                        onClick={() =>
                          setCurrentPage(currentPage - 1)
                        }
                      >
                        Prev
                      </button>
                    </li>

                    {/* số trang */}
                    {[...Array(lastPage)].map((_, index) => (
                      <li key={index}>
                        <button
                          onClick={() =>
                            setCurrentPage(index + 1)
                          }
                          style={{
                            fontWeight:
                              currentPage === index + 1
                                ? "bold"
                                : "normal"
                          }}
                        >
                          {index + 1}
                        </button>
                      </li>
                    ))}

                    {/* next */}
                    <li>
                      <button
                        disabled={currentPage === lastPage}
                        onClick={() =>
                          setCurrentPage(currentPage + 1)
                        }
                      >
                        Next
                      </button>
                    </li>

                  </ul>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Blog;