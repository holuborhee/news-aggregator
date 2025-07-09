import { Article } from "../app/page";

export function ArticleCard({ article }: { article: Article }) {
  return (
    <a
      href={article.url}
      target="_blank"
      rel="noopener noreferrer"
      className="block p-4 border rounded-lg hover:shadow-md transition bg-white"
    >
      <h2 className="text-lg font-semibold">{article.title}</h2>
      <p className="text-gray-500 text-sm">
        {article.source.name} •{" "}
        {new Date(article.published_at).toLocaleString()}
      </p>
      <p className="mt-2 text-gray-700 line-clamp-2">{article.description}</p>
    </a>
  );
}
