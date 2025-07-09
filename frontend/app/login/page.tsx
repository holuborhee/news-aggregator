"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";
import { loginUser } from "@/lib/auth";
import Link from "next/link";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errors, setErrors] = useState<Record<string, string>>({});
  const router = useRouter();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await loginUser({ email, password });
      router.push("/feeds");
    } catch (err) {
      const data = JSON.parse(err.message);
      if (data.errors) {
        const mappedErrors: Record<string, string> = {};
        Object.entries(data.errors).forEach(([field, messages]) => {
          mappedErrors[field] = (messages as string[])[0];
        });
        setErrors(mappedErrors);
      }
    }
  };

  return (
    <form onSubmit={handleLogin} className="max-w-md mx-auto mt-10">
      <div className="mb-4">
        <label>Email</label>
        <input
          type="email"
          className="w-full border p-2"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        {errors.email && <p className="text-red-500 text-sm">{errors.email}</p>}
      </div>
      <div className="mb-4">
        <label>Password</label>
        <input
          type="password"
          className="w-full border p-2"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        {errors.password && (
          <p className="text-red-500 text-sm">{errors.password}</p>
        )}
      </div>
      <button className="bg-blue-600 text-white px-4 py-2">Login</button>
      <p className="mt-4 text-sm text-center">
        Don't have an account?{" "}
        <Link href="/register" className="text-blue-600 hover:underline">
          Sign up
        </Link>
      </p>
    </form>
  );
}
