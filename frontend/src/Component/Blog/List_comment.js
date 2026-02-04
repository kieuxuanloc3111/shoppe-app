
import React from "react";

const buildTree = (comments) => {
  // map id -> node
  const map = {};
  const roots = [];

  comments.forEach((c) => {
    // ensure shallow copy
    map[c.id] = { ...c, children: [] };
  });

  Object.values(map).forEach((node) => {
    const parentId = node.id_comment;
    if (parentId && map[parentId]) {
      map[parentId].children.push(node);
    } else {
      roots.push(node);
    }
  });

  // optionally sort by created_at descending so newest first
  const sortFn = (a, b) => {
    // if created_at missing, treat as newest
    const ta = a.created_at ? new Date(a.created_at).getTime() : Date.now();
    const tb = b.created_at ? new Date(b.created_at).getTime() : Date.now();
    return tb - ta;
  };

  const sortRecursive = (arr) => {
    arr.sort(sortFn);
    arr.forEach((n) => {
      if (n.children && n.children.length) sortRecursive(n.children);
    });
  };

  sortRecursive(roots);
  return roots;
};

const CommentNode = ({ node, depth = 0, onReply, replyTargetId }) => {
  const indent = depth * 40;

  const highlightStyle = node.id === replyTargetId
    ? { boxShadow: "0 0 0 3px rgba(0,123,255,0.08)", borderLeft: "4px solid #007bff", background: "#f8fbff" }
    : {};

  return (
    <div style={{ marginLeft: indent, marginBottom: 16, ...highlightStyle }}>
      <div style={{ display: "flex", gap: 12 }}>
        <div>
          <img
            src={
              node.image_user
                ? `http://localhost/laravel8/laravel8/public/upload/user/avatar/${node.image_user}`
                : "/mnt/data/221ae488-3ff1-4844-9299-2ed1af1a81f5.png"
            }
            alt={node.name_user}
            style={{ width: 50, height: 50, objectFit: "cover", borderRadius: 4 }}
          />
        </div>

        <div style={{ flex: 1 }}>
          <div style={{ marginBottom: 6 }}>
            <strong>{node.name_user}</strong>{" "}
            <span style={{ color: "#666", marginLeft: 8 }}>{node.created_at ?? "Vừa xong"}</span>
          </div>

          <div style={{ marginBottom: 8 }}>{node.comment}</div>

          <div>
            <button
              className="btn btn-sm btn-outline-primary"
              onClick={() => onReply(node)}
              style={{ padding: "6px 10px" }}
              type="button"
            >
              <i className="fa fa-reply" /> Reply
            </button>
          </div>
        </div>
      </div>

      {/* children */}
      {node.children && node.children.length > 0 && (
        <div style={{ marginTop: 12 }}>
          {node.children.map((child) => (
            <CommentNode
              key={child.id}
              node={child}
              depth={depth + 1}
              onReply={onReply}
              replyTargetId={replyTargetId}
            />
          ))}
        </div>
      )}
    </div>
  );
};

const ListComment = ({ comments = [], onReply, replyTargetId = null }) => {
  const safeComments = Array.isArray(comments) ? comments : [];

  if (safeComments.length === 0) {
    return (
      <div className="response-area">
        <h2>0 Comments</h2>
        <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
      </div>
    );
  }

  const tree = buildTree(safeComments);

  // total count
  const count = safeComments.length;

  return (
    <div className="response-area">
      <h2>{count} Comments</h2>

      <div className="media-list">
        {tree.map((root) => (
          <CommentNode
            key={root.id}
            node={root}
            depth={0}
            onReply={onReply}
            replyTargetId={replyTargetId}
          />
        ))}
      </div>
    </div>
  );
};

export default ListComment;
