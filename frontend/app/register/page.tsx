"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";
import { registerUser } from "@/lib/auth";
import Link from "next/link";

export default function RegisterPage() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [password_confirmation, setPasswordConfirmation] = useState("");
  const [errors, setErrors] = useState<Record<string, string>>({});
  const router = useRouter();

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await registerUser({ name, email, password, password_confirmation });
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
    <form onSubmit={handleRegister} className="max-w-md mx-auto mt-10">
      <div className="mb-4">
        <label>Name</label>
        <input
          type="text"
          className="w-full border p-2"
          value={name}
          onChange={(e) => setName(e.target.value)}
        />
        {errors.name && <p className="text-red-500 text-sm">{errors.name}</p>}
      </div>
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
      <div className="mb-4">
        <label>Confirm Password</label>
        <input
          type="password"
          className="w-full border p-2"
          value={password_confirmation}
          onChange={(e) => setPasswordConfirmation(e.target.value)}
        />
      </div>
      <button className="bg-green-600 text-white px-4 py-2">Sign Up</button>
      <p className="mt-4 text-sm text-center">
        Already have an account?{" "}
        <Link href="/login" className="text-blue-600 hover:underline">
          Log in
        </Link>
      </p>
    </form>
  );
}
